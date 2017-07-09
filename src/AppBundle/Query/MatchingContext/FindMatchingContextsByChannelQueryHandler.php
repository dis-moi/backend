<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 30/06/2017
 * Time: 16:57
 */

namespace AppBundle\Query\MatchingContext;


use AppBundle\Entity\BrowserExtension\MatchingContextFactory;
use AppBundle\Repository\MatchingContextRepository;

class FindMatchingContextsByChannelQueryHandler
{
    /**
     * @var MatchingContextRepository
     */
    private $repository;
    /**
     * @var MatchingContextFactory
     */
    private $factory;

    /**
     * FindMatchingContextsByChannelQueryHandler constructor.
     * @param MatchingContextRepository $repository
     */
    public function __construct(MatchingContextRepository $repository, MatchingContextFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param FindMatchingContextsByChannelQuery $query
     * @return array
     */
    public function handle(FindMatchingContextsByChannelQuery $query)
    {
        $matchingContexts = $this->repository->findAllPublicByChannel($query->channelName, $query->criterion);

        return $this->factory->createFromMatchingContexts($matchingContexts);
    }
}