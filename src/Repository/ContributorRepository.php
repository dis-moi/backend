<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Subscription;
use App\Helper\NoticeVisibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ContributorRepository extends ServiceEntityRepository
{
    /**
     * @var NoticeRepository
     */
    protected $noticeRepository;

    public function __construct(ManagerRegistry $registry, NoticeRepository $noticeRepository)
    {
        parent::__construct($registry, Contributor::class);

        $this->noticeRepository = $noticeRepository;
    }

    public static function addActiveSubscriptionsCount(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
        ->addSelect('count(s.extension) as activeSubscriptions')
        ->leftJoin('c.subscriptions', 's', Join::WITH, 's.created >= :freshnessDate OR s.updated >= :freshnessDate')
        ->groupBy('c.id')
        ->setParameter('freshnessDate', Subscription::getFreshnessDate());
    }

    /**
     * @param mixed[]|null $result
     */
    public static function mergeActiveSubscriptionsCountWithContributor(?array $result): ?Contributor
    {
        if ( ! $result || ! $result[0]) {
            return null;
        }

        /** @var Contributor $contributor */
        $contributor = $result[0];
        $contributor->setActiveSubscriptionsCount((int) $result['activeSubscriptions']);

        return $contributor;
    }

    /**
     * @return Contributor[]
     */
    public function getAll(): array
    {
        $mainQuery = $this->createQueryBuilder('c');
        $resultsWithActiveSubscriptionsCount = self::addActiveSubscriptionsCount($mainQuery)
            ->getQuery()
            ->getResult();

        return array_map('self::mergeActiveSubscriptionsCountWithContributor', $resultsWithActiveSubscriptionsCount);
    }

    /**
     * @return Contributor[]
     */
    public function getAllEnabledWithAtLeastOneContribution(): array
    {
        $activeContributorsQuery = $this->noticeRepository->createQueryBuilder('n')
            ->select('IDENTITY(n.contributor)')->distinct()
            ->where('n.visibility = :visibility');

        $mainQuery = $this->createQueryBuilder('c');
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Contributor | null
     */
    public function getOne(int $id): ?Contributor
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andwhere('c.enabled = true')
            ->setParameter('id', $id);

        $queryResult = self::addActiveSubscriptionsCount($queryBuilder)
          ->getQuery()
          ->getOneOrNullResult();

        return self::mergeActiveSubscriptionsCountWithContributor($queryResult);
    }

    public static function getOrderedList(EntityRepository $er): QueryBuilder
    {
        return $er->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');
    }
}
