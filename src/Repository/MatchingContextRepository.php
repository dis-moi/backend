<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MatchingContext;
use App\Helper\NoticeVisibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class MatchingContextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchingContext::class);
    }

    /**
     * @return MatchingContext[]
     */
    public function findAllWithPrivateVisibility(): array
    {
        return $this->createQueryBuilder('mc')
            ->leftJoin('mc.notice', 'n')
            ->where('n.visibility = :visibility')
            ->setParameter('visibility', NoticeVisibility::PRIVATE_VISIBILITY())
            ->getQuery()->execute();
    }

    /**
     * @param int[] $contributors
     *
     * @return MatchingContext[]
     */
    public function findAllPublicMatchingContext(array $contributors = null): array
    {
        $queryBuilder = $this->createQueryForPublicMatchingContexts('mc', 'n', 'c');

        if ($contributors) {
            $queryBuilder
                ->andWhere('c.id IN (:contributors) OR rc.id IN (:contributors)')
                ->setParameter('contributors', $contributors);
        }

        return $queryBuilder->getQuery()->execute();
    }

    public function createQueryForPublicMatchingContexts(string $matchingContextAlias = 'mc', string $noticeAlias = 'n', string $contributorAlias = 'c'): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($matchingContextAlias)
            ->select($matchingContextAlias)
            ->leftJoin("$matchingContextAlias.notice", $noticeAlias)
            ->leftJoin("$noticeAlias.contributor", $contributorAlias)
            ->leftJoin('App:Relay', 'r', Join::WITH, "r.notice = $noticeAlias.id")
            ->leftJoin('r.relayedBy', 'rc')
            ->andWhere("$contributorAlias.enabled = true");

        return NoticeRepository::addNoticeVisibilityLogic($queryBuilder, 'n');
    }
}
