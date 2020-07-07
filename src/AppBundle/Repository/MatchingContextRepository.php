<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MatchingContext;
use AppBundle\Helper\NoticeVisibility;
use Doctrine\ORM\QueryBuilder;

class MatchingContextRepository extends BaseRepository
{
    public function findAllWithPrivateVisibility(): array
    {
        return $this->repository->createQueryBuilder('mc')
            ->leftJoin('mc.notice', 'n')
            ->where('n.visibility = :visibility')
            ->setParameter('visibility', NoticeVisibility::PRIVATE_VISIBILITY())
            ->getQuery()->execute();
    }

    public function findAllPublicMatchingContext(array $contributors = null): array
    {
        $queryBuilder = $this->createQueryForPublicMatchingContexts('mc', 'n', 'c');

        if ($contributors) {
            $queryBuilder
                ->andWhere('c.id IN (:contributors)')
                ->setParameter('contributors', $contributors)
            ;
        }

        return $queryBuilder->getQuery()->execute();
    }

    public function createQueryForPublicMatchingContexts($matchingContextAlias = 'mc', $noticeAlias = 'n', $contributorAlias = 'c'): QueryBuilder
    {
        $queryBuilder = $this->repository->createQueryBuilder($matchingContextAlias)
            ->select($matchingContextAlias)
            ->leftJoin("$matchingContextAlias.notice", $noticeAlias)
            ->leftJoin("$noticeAlias.contributor", $contributorAlias)
            ->andWhere("$contributorAlias.enabled = true")
        ;

        return NoticeRepository::addNoticeVisibilityLogic($queryBuilder, 'n');
    }

    public function getResourceClassName(): string
    {
        return MatchingContext::class;
    }
}
