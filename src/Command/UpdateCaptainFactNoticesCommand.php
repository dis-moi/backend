<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Contributor;
use App\Entity\DomainName;
use App\Entity\MatchingContext;
use App\Entity\Notice;
use App\Helper\NoticeVisibility;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    public const NOTICE_EXTERNAL_ID_PREFIX = 'CF_';
    public const YOUTUBE_DOMAIN_NAME = 'www.youtube.com';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var int
     */
    private $contributorId;

    /**
     * @var Contributor
     */
    private $contributor;

    /**
     * @var array<string, string>
     */
    private $noticesExternalIds;

    /**
     * @var DomainName
     */
    private $youtubeDomainName;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, int $contributorId)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->contributorId = $contributorId;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Update the Captain Fact auto-generated notices based on Captain Fact\'s GraphQL API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $startTimestamp = time();

        // Load CaptainFact contributor
        try {
            $this->loadCaptainFactContributor();
        } catch (Exception $e) {
            $this->output->writeln($e->getMessage());

            return 1;
        }

        // Load Existing CaptainFact notices
        $this->loadExternalIdsOfNoticesForCaptainFactContributor();

        // Load Youtube Domain Name
        $this->loadYoutubeDomainName();

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
            $entriesCount = \count($content['data']['videos']['entries']);
            for ($entryIndex = 0; $entryIndex < $entriesCount; ++$entryIndex) {
                $entry = $content['data']['videos']['entries'][$entryIndex];

                $sourcesCount = 0;
                if ($this->isEntryEligible($entry, $sourcesCount)) {
                    $this->computeEntry($entry, $sourcesCount, $creationCount, $updateCount);
                }
            }

            ++$pageIndex;
        } while (0 != $entriesCount); // stop if there weren't any entries in this page
        $this->output->writeln('No more entries to load.');

        // Disable (set visibility = archived) current notices for unworthy or disabled videos
        $archiveCount = 0;
        $this->disableNoticesWithExternalIds($archiveCount);

        $this->output->writeln(sprintf('Done in '.(time() - $startTimestamp).' seconds. %d notice(s) created, %d updated, %d archived.', $creationCount, $updateCount, $archiveCount));

        return 0;
    }

    /**
     * Method loadCaptainFactContributor
     * Load contributor defined in environment variable set in set::ENV_KEY.
     */
    protected function loadCaptainFactContributor(): void
    {
        if (0 == (int) $this->contributorId) {
            throw new Exception('You have to set the DISMOI_CAPTAINFACT_CONTRIBUTOR_ID environment variable.');
        }

        $this->contributor = $this
            ->entityManager
            ->find('App\Entity\Contributor', (int) $this->contributorId);

        if (!$this->contributor) {
            throw new Exception("Contributor with id $this->contributorId was not found.");
        }
    }

    /**
     * Method loadExternalIdsOfNoticesForCaptainFactContributor
     * Load externalId of existing notices for the given contributor.
     */
    protected function loadExternalIdsOfNoticesForCaptainFactContributor(): void
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
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Method loadYoutubeDomainName
     * Load youtube domain name or create it if necessary.
     */
    protected function loadYoutubeDomainName(): void
    {
        $this->youtubeDomainName = $this
            ->entityManager
            ->createQuery("SELECT dn FROM App\Entity\DomainName dn WHERE dn.name=:name")
            ->setParameter('name', self::YOUTUBE_DOMAIN_NAME)
            ->getResult();

        if (!$this->youtubeDomainName) {
            $this->output->writeln('Youtube domain name needs to be created');

            $this->youtubeDomainName = new DomainName();
            $this->youtubeDomainName->setName(self::YOUTUBE_DOMAIN_NAME);

            $this
                ->entityManager
                ->persist($this->youtubeDomainName);

            $this
                ->entityManager
                ->flush();
        }
    }

    /**
     * Method fetchVideosForPage
     * Fetch a page of videos from CaptainFact API.
     *
     * @return mixed[]
     */
    protected function fetchVideosForPage(int $pageIndex): array
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
     *
     * @param mixed[] $entry
     */
    protected function isEntryEligible(array $entry, int &$sourcesCount): bool
    {
        // No need to go further is there isn't any associated video
        if ('' == $entry['youtubeId']) {
            return false;
        }
        $minSourcesCount = 8;
        $minScore = 1;
        $minSourcesCountWithMinScore = 5;

        $hasScoredSource = false;
        $deserves = false;

        for ($statementIndex = 0; $statementIndex < \count($entry['statements']); ++$statementIndex) {
            for ($commentIndex = 0; $commentIndex < \count($entry['statements'][$statementIndex]['comments']); ++$commentIndex) {
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
     *
     * @param mixed[] $entry
     */
    protected function computeEntry(array $entry, int $sourcesCount, int &$creationCount, int &$updateCount): void
    {
        $matchingContexts = [];
        $matchingContext = new MatchingContext();
        $matchingContext->addDomainName($this->youtubeDomainName);
        $matchingContext->setUrlRegex('/watch\?v='.$entry['youtubeId'].'.*');
        $matchingContext->setExampleUrl('https://www.youtube.com/watch?v='.$entry['youtubeId']);
        $matchingContext->setDescription('Vidéo Youtube');

        $message = 'Cette vidéo est en cours de fact-checking collaboratif sur CaptainFact ('.$sourcesCount.' commentaire'.(($sourcesCount) ? 's' : '').' sourcé'.(($sourcesCount) ? 's' : '').' au '.date('d/m/Y').'). <a href="https://captainfact.io/videos/'.$entry['hashId'].'">Voir les résultats</a>';

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
    protected function createNoticeWithExternalId(string $externalId, Contributor $contributor, MatchingContext $matchingContext, string $message): void
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
    protected function updateNoticesWithExternalId(string $externalId, Contributor $contributor, MatchingContext $matchingContext, string $message): void
    {
        $this->output->writeln(sprintf('... update entry with id %d', $externalId));

        $notices = $this
            ->entityManager
            ->createQuery("SELECT n FROM App\Entity\Notice n WHERE n.contributor=:contributor AND n.externalId=:externalId")
            ->setParameter('contributor', $contributor)
            ->setParameter('externalId', self::NOTICE_EXTERNAL_ID_PREFIX.$externalId)
            ->getResult();

        for ($noticeIndex = 0; $noticeIndex < \count($notices); ++$noticeIndex) {
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
    protected function disableNoticesWithExternalIds(int &$archiveCount): void
    {
        while (\count($this->noticesExternalIds) > 0) {
            $noticeExternalId = array_shift($this->noticesExternalIds);
            $notices = $this
                ->entityManager
                ->createQuery("SELECT n FROM App\Entity\Notice n WHERE n.externalId=:eid")
                ->setParameter('eid', $noticeExternalId)
                ->getResult();

            for ($noticeIndex = 0; $noticeIndex < \count($notices); ++$noticeIndex) {
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
