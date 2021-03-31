<?php

namespace App\Command;

use App\Entity\MatchingContext;
use App\Entity\Notice;
use App\Helper\NoticeVisibility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class UpdateCaptainFactNoticesCommand.
 */
class UpdateCaptainFactNoticesCommand extends Command
{
    protected static $defaultName = 'app:notices:update:captainfact';

    const NOTICE_EXTERNAL_ID_PREFIX = 'CF_';

    private $httpClient;
    private $entityManager;
    private $contributorId;

    private $contributor;
    private $noticesExternalIds;
    private $output;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, $contributorId)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->contributorId = $contributorId;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Update the Captain Fact auto-generated notices based on Captain Fact\'s GraphQL API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $startTimestamp = time();

        // Load CaptainFact contributor
        try {
            $this->loadCaptainFactContributor();
        } catch (Exception $e) {
            $this->output->writeln('Error: you have to set the DISMOI_CAPTAINFACT_CONTRIBUTOR_ID environment variable.');

            return;
        }

        // Load Existing CaptainFact notices
        $this->loadExternalIdsOfNoticesForCaptainFactContributor();

        // CF API calls: loop on online videos and create/update notices for worthy ones
        $pageIndex = 1;
        $creationCount = 0;
        $updateCount = 0;
        $firstIndexOfLastPage = 0;
        do {
            // Fetch videos page
            $content = $this->fetchVideosForPage($pageIndex);

            // hack: the API gives the last page idefinitely and we don't want it
            if ($content['data']['videos']['entries'][0]['id'] == $firstIndexOfLastPage) {
                break;
            }
            $firstIndexOfLastPage = $content['data']['videos']['entries'][0]['id'];

            // For each eligible entry, create or update a notice
            $entriesCount = count($content['data']['videos']['entries']);
            for ($entryIndex = 0; $entryIndex < $entriesCount; ++$entryIndex) {
                $entry = $content['data']['videos']['entries'][$entryIndex];

                $sourcesCount = 0;
                if ($this->isEntryEligible($entry, $sourcesCount)) {
                    $this->computeEntry($entry, $sourcesCount, $creationCount, $updateCount);
                }
            }

            ++$pageIndex;
        } while (0 != $entriesCount); /* stop if there weren't any entries in this page */
        $this->output->writeln('No more entries to load.');

        // Disable (set visibility = archived) current notices for unworthy or disabled videos
        $archiveCount = 0;
        $this->disableNoticesWithExternalIds($archiveCount);

        $this->output->writeln(sprintf('Done in '.(time() - $startTimestamp).' seconds. %d notice(s) created, %d updated, %d archived.', $creationCount, $updateCount, $archiveCount));
    }

    /**
     * Method loadCaptainFactContributor
     * Load contributor defined in environment variable set in set::ENV_KEY.
     */
    protected function loadCaptainFactContributor()
    {
        if (0 == (int) $this->contributorId) {
            throw new Exception();
        }

        $this->contributor = $this
            ->entityManager
            ->find('App\Entity\Contributor', (int) $this->contributorId);
    }

    /**
     * Method loadExternalIdsOfNoticesForCaptainFactContributor
     * Load externalId of existing notices for the given contributor.
     */
    protected function loadExternalIdsOfNoticesForCaptainFactContributor()
    {
        try {
            $this->noticesExternalIds = array_flip(
                array_column(
                    $this
                        ->entityManager
                        ->createQuery("SELECT DISTINCT n.externalId FROM App\Entity\Notice n WHERE n.contributor=:contributor AND n.externalId LIKE :externalIdPattern")
                        ->setParameter('contributor', $this->contributor)
                        ->setParameter('externalIdPattern', self::NOTICE_EXTERNAL_ID_PREFIX.'%')
                        ->getScalarResult(),
                    'externalId'
                )
            );
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Method fetchVideosForPage
     * Fetch a page of videos from CaptainFact API.
     */
    protected function fetchVideosForPage($pageIndex): array
    {
        $this->output->writeln(sprintf('Loading videos page %d...', $pageIndex));

        $response = $this->httpClient->request(
            'POST',
            'https://graphql.captainfact.io/graphiql',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'query' => sprintf('
                        {videos(offset:%d) {
                            totalEntries,
                            entries {
                                id,
                                hashId,
                                title,
                                youtubeId,
                                url,
                                statements {
                                    id, text,
                                    comments {
                                        id,
                                        approve,
                                        text,
                                        score,
                                        source {
                                            id
                                        }
                                    }
                                }
                            }
                        }
                    }', $pageIndex),
                ],
            ]
        );
        $statusCode = $response->getStatusCode();
        if (200 != $statusCode) {
            throw new Exception(sprintf('API status code %d', $statusCode));
        }

        return $response->toArray();
    }

    /**
     * Method isEntryEligible
     * Check if entry deserves a notice: if there is at least $minSourcesCount sources, or $minSourcesCountWithMinScore if one of them scores at least $minScore.
     */
    protected function isEntryEligible($entry, &$sourcesCount): bool
    {
        $minSourcesCount = 8;
        $minScore = 1;
        $minSourcesCountWithMinScore = 5;

        $hasScoredSource = false;
        $deserves = false;

        for ($statementIndex = 0; $statementIndex < count($entry['statements']); ++$statementIndex) {
            for ($commentIndex = 0; $commentIndex < count($entry['statements'][$statementIndex]['comments']); ++$commentIndex) {
                ++$sourcesCount;

                if ($sourcesCount >= $minSourcesCount) {
                    $deserves = true;
                } elseif (!$deserves) {
                    if ($entry['statements'][$statementIndex]['comments'][$commentIndex]['score'] >= $minScore) {
                        $hasScoredSource = true;
                    }
                    if ($hasScoredSource && ($sourcesCount >= $minSourcesCountWithMinScore)) {
                        $deserves = true;
                    }
                }
            }
        }

        return $deserves;
    }

    /**
     * Method computeEntry
     * Create or update a notice with the given entry.
     */
    protected function computeEntry($entry, $sourcesCount, &$creationCount, &$updateCount)
    {
        $matchingContexts = [];
        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex('https://www.youtube.com/watch?v='.$entry['youtubeId'].'(&.*)?$');

        $message = 'Cette vidéo est en cours de fact-checking collaboratif sur CaptainFact ('.$sourcesCount.' commentaire'.(($sourcesCount) ? 's' : '').' sourcés'.(($sourcesCount) ? 's' : '').' au '.date('d/m/Y').'). <a href="https://captainfact.io/videos/'.$entry['hashId'].'">Voir les résultats</a>';

        if (isset($this->noticesExternalIds[self::NOTICE_EXTERNAL_ID_PREFIX.$entry['id']])) {
            $this->updateNoticesWithExternalId($entry['id'], $this->contributor, $matchingContext, $message);
            ++$updateCount;

            // Remove this external id from the remaining existing notices
            unset($this->noticesExternalIds[self::NOTICE_EXTERNAL_ID_PREFIX.$entry['id']]);
        } else {
            $this->createNoticeWithExternalId($entry['id'], $this->contributor, $matchingContext, $message);
            ++$creationCount;
        }

        $this
            ->entityManager
            ->flush();
    }

    /**
     * Method createNoticeForExternalId
     * Create a notice with the given parameters.
     */
    protected function createNoticeWithExternalId($externalId, $contributor, $matchingContext, $message)
    {
        $this->output->writeln(sprintf('... create entry with id %d', $externalId));

        $notice = new Notice();
        $notice->setExternalId(self::NOTICE_EXTERNAL_ID_PREFIX.$externalId);
        $notice->setContributor($contributor);
        $notice->addMatchingContext($matchingContext);
        $notice->setMessage($message);
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());

        $this
            ->entityManager
            ->persist($notice);
    }

    /**
     * Method updateNoticesForExternalId
     * Update the notice with the given externalId with the the given parameters.
     */
    protected function updateNoticesWithExternalId($externalId, $contributor, $matchingContext, $message)
    {
        $this->output->writeln(sprintf('... update entry with id %d', $externalId));

        $notices = $this
            ->entityManager
            ->createQuery("SELECT n FROM App\Entity\Notice n WHERE n.contributor=:contributor AND n.externalId=:externalId")
            ->setParameter('contributor', $contributor)
            ->setParameter('externalId', self::NOTICE_EXTERNAL_ID_PREFIX.$externalId)
            ->getResult();

        for ($noticeIndex = 0; $noticeIndex < count($notices); ++$noticeIndex) {
            $notices[$noticeIndex]->setMessage($message);
            $notices[$noticeIndex]->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());

            $this
                ->entityManager
                ->persist($notices[$noticeIndex]);
        }
    }

    /**
     * Method disableNoticesWithExternalIds
     * Update the remaining notices to set their visibility to archived.
     */
    protected function disableNoticesWithExternalIds(&$archiveCount)
    {
        while (count($this->noticesExternalIds) > 0) {
            $noticeExternalId = array_shift($this->noticesExternalIds);
            $notices = $this
                ->entityManager
                ->createQuery("SELECT n FROM App\Entity\Notice n WHERE n.externalId=:eid")
                ->setParameter('eid', $noticeExternalId)
                ->getResult();

            for ($noticeIndex = 0; $noticeIndex < count($notices); ++$noticeIndex) {
                $notices[$noticeIndex]->setVisibility(NoticeVisibility::ARCHIVED_VISIBILITY());
                $this
                    ->entityManager
                    ->persist($notices[$noticeIndex]);
            }

            $this->output->writeln(sprintf('... archive entry %s', substr($noticeExternalId, -3)));
            ++$archiveCount;
        }

        $this
            ->entityManager
            ->flush();
    }
}
