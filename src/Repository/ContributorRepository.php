<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Subscription;
use App\Helper\NoticeVisibility;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function Doctrine\ORM\QueryBuilder;

class ContributorRepository extends BaseRepository
{
    protected $noticeRepository;

    public function __construct(EntityManagerInterface $entityManager, NoticeRepository $noticeRepository)
    {
        parent::__construct($entityManager);

        $this->noticeRepository = $noticeRepository;
    }

    public static function addActiveSubscriptionsCount(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
        ->addSelect('count(s.extension) as activeSubscriptions')
        ->leftJoin('c.subscriptions', 's', Join::WITH, 's.created >= :freshnessDate OR s.updated >= :freshnessDate')
        ->groupBy('c.id')
        ->setParameter('freshnessDate', Subscription::getFreshnessDate());
    }

    public static function mergeActiveSubscriptionsCountWithContributor($result)
    {
        if (!$result || !$result[0]) {
            return null;
        }

        /** @var Contributor $contributor */
        $contributor = $result[0];
        $contributor->setActiveSubscriptionsCount($result['activeSubscriptions']);

        return $contributor;
    }

    public function getAll()
    {
        $mainQuery = $this->repository->createQueryBuilder('c');
        $resultsWithActiveSubscriptionsCount = self::addActiveSubscriptionsCount($mainQuery)
            ->getQuery()
            ->getResult();

        return array_map('self::mergeActiveSubscriptionsCountWithContributor', $resultsWithActiveSubscriptionsCount);
    }

    public function getAllEnabledWithAtLeastOneContribution()
    {
        $activeContributorsQuery = $this->noticeRepository->repository->createQueryBuilder('n')
            ->select('IDENTITY(n.contributor)')->distinct()
            ->where('n.visibility = :visibility');

        $mainQuery = $this->repository->createQueryBuilder('c');
        $mainQuery = $mainQuery
            ->where('c.enabled = true')
            ->andWhere($mainQuery->expr()->in('c.id', $activeContributorsQuery->getDQL()))
            ->setParameter('visibility', NoticeVisibility::PUBLIC_VISIBILITY());

        $resultsWithActiveSubscriptionsCount = self::addActiveSubscriptionsCount($mainQuery)
          ->getQuery()
          ->getResult();

        return array_map('self::mergeActiveSubscriptionsCountWithContributor', $resultsWithActiveSubscriptionsCount);
    }

    /**
     * @return Contributor | null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOne(int $id)
    {
        $queryBuilder = $this->repository->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andwhere('c.enabled = true')
            ->setParameter('id', $id);

        $queryResult = self::addActiveSubscriptionsCount($queryBuilder)
          ->getQuery()
          ->getOneOrNullResult();

        return self::mergeActiveSubscriptionsCountWithContributor($queryResult);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return Contributor::class;
    }

    public static function getOrderedList(EntityRepository $er)
    {
        return $er->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');
    }
}
