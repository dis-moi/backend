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
 *
 * TODO
 * format des videos youtube a gerer (XXXXXX = youtubeId) :
 * - https://www.youtube.com/watch?v=XXXXXX
 * - https://www.youtube.com/embed/XXXXXX (TO CONFIRM)
 *
 * format de la page de redirection captain fact (YYYYYY = hashId)
 * - https://captainfact.io/videos/YYYYYY
 */
class UpdateCaptainFactNoticesCommand extends Command
{
    protected static $defaultName = 'app:notices:update:captainfact';

    public static $settingKey = 'CF';
    public static $noticeExternalIdPrefix = 'CF_';

    private $httpClient;
    private $entityManager;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Update the Captain Fact auto-generated notices based on Captain Fact\'s GraphQL API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Fetching Captain Fact\'s GraphQL API...');
        $timestamp = time();

        // Get CF contributor's id
        try {
            $settings = json_decode(
                $this
                    ->entityManager
                    ->find('App\Entity\Setting', self::$settingKey)
                    ->getValue(),
                true
            );
        } catch (Exception $e) {
            $output->writeln('Error: unable to load CF settings');

            return;
        }

        // Get CF contributor
        $contributor = $this
            ->entityManager
            ->find('App\Entity\Contributor', (int) $settings['contributorId']);

        // Read current external IDs of CF notices
        $noticesIds = array_flip(
            array_column(
                $this
                    ->entityManager
                    ->createQuery("SELECT DISTINCT n.externalId FROM App\Entity\Notice n WHERE n.contributor=:contributor AND n.externalId LIKE :externalIdPattern")
                    ->setParameter('contributor', $contributor)
                    ->setParameter('externalIdPattern', self::$noticeExternalIdPrefix.'%')
                    ->getScalarResult(),
                'externalId'
            )
        );

        // CF API calls: loop on online videos and create/update notices for worthy ones
        $creationCount = 0;
        $updateCount = 0;
        $archiveCount = 0;

        $pageIndex = 1;
        do {
            $output->writeln(sprintf('Loading videos page %d...', $pageIndex));

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
                                    id, hashId, title, youtubeId, url,
                                    statements {
                                        id, text,
                                        comments {
                                            id, approve, text, score
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
                $output->writeln(sprintf('API status code %d', $statusCode));

                return;
            }
            $content = $response->toArray();

            $entriesCount = count($content['data']['videos']['entries']);
            for ($entryIndex = 0; $entryIndex < $entriesCount; ++$entryIndex) {
                $entry = $content['data']['videos']['entries'][$entryIndex];

                // Determine if this entry deserves a notice
                $minCommentsCount = 3;
                $minVotesCounts = 1;

                $commentsCount = 0;
                $votesCount = 0;
                $deserves = false;

                for ($statementIndex = 0; $statementIndex < count($entry['statements']); ++$statementIndex) {
                    $commentsCount += count($entry['statements'][$statementIndex]['comments']);
                    if ($commentsCount >= $minCommentsCount) {
                        $deserves = true;
                    } else {
                        for ($k = 0; $k < count($entry['statements'][$statementIndex]['comments']); ++$k) {
                            $votesCount += $entry['statements'][$statementIndex]['comments'][$k]['score'];
                            if ($votesCount >= $minVotesCounts) {
                                $deserves = true;
                                break;
                            }
                        }
                    }
                }
                if ($deserves) {
                    // Create the matching context
                    $matchingContexts = [];
                    $matchingContext = new MatchingContext();
                    $matchingContext->setUrlRegex('https://www.youtube.com/watch?v='.$entry['youtubeId'].'(&.*)?$');

                    // Create the message
                    $message = 'Cette vidéo est en cours de fact-checking collaboratif sur CaptainFact ('.$commentsCount.' commentaire'.(($commentsCount) ? 's' : '').' au '.date('d/m/Y').'). <a href="https://captainfact.io/videos/'.$entry['hashId'].'">Voir les résultats</a>';

                    if (isset($noticesIds[self::$noticeExternalIdPrefix.$entry['id']])) {
                        // Update an existing notice
                        $notices = $this
                            ->entityManager
                            ->createQuery("SELECT n FROM App\Entity\Notice n WHERE n.contributor=:contributor AND n.externalId=:externalId")
                            ->setParameter('contributor', $contributor)
                            ->setParameter('externalId', self::$noticeExternalIdPrefix.$entry['id'])
                            ->getResult();
                        for ($noticeIndex = 0; $noticeIndex < count($notices); ++$noticeIndex) {
                            $notices[$noticeIndex]->setMessage($message);
                            $notices[$noticeIndex]->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
                            $this
                                ->entityManager
                                ->persist($notices[$noticeIndex]);
                        }
                        unset($noticesIds[self::$noticeExternalIdPrefix.$entry['id']]);

                        $output->writeln(sprintf('... update entry %d', $entry['id']));
                        ++$updateCount;
                    } else {
                        // Create a new notice
                        $notice = new Notice();
                        $notice->setContributor($contributor);
                        $notice->setExternalId(self::$noticeExternalIdPrefix.$entry['id']);
                        $notice->addMatchingContext($matchingContext);
                        $notice->setMessage($message);
                        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
                        $this
                            ->entityManager
                            ->persist($notice);

                        $output->writeln(sprintf('... create entry %d', $entry['id']));
                        ++$creationCount;
                    }
                } else {
                    $output->writeln(sprintf('... ignore entry %d', $entry['id']));
                }
            }
            $this
                ->entityManager
                ->flush();

            ++$pageIndex;
        } while (0 != $entriesCount);

        // Disable (set visibility = archived) current notices for unworthy or disabled videos
        while (count($noticesIds) > 0) {
            $noticeExternalId = array_shift($noticesIds);
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

            $output->writeln(sprintf('... archive entry %s', substr($noticeExternalId, -3)));
            ++$archiveCount;
        }
        $this
            ->entityManager
            ->flush();

        $output->writeln(sprintf('Done in '.(time() - $timestamp).' seconds. %d notice(s) created, %d updated, %d archived.', $creationCount, $updateCount, $archiveCount));
    }
}
