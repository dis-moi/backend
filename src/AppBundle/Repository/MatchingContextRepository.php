<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MatchingContext;
use AppBundle\Helper\NoticeVisibility;
use Doctrine\ORM\QueryBuilder;

class MatchingContextRepository extends BaseRepository
{
    /**
     * @return array
     */
    public function findAllWithPrivateVisibility()
    {
        return $this->repository->createQueryBuilder('mc')
            ->leftJoin('mc.notice', 'n')
            ->where('n.visibility = :visibility')
            ->setParameter('visibility', NoticeVisibility::PRIVATE_VISIBILITY())
            ->getQuery()->execute();
    }

    /**
     * @param array|null $contributors
     * @return array
     */
    public function findAllPublicMatchingContext(array $contributors = null)
    {
        $queryBuilder = $this->createQueryForPublicMatchingContexts();

        if ($contributors) {
            $queryBuilder
                ->andWhere('contributor.id IN (:contributors)')
                ->setParameter('contributors', $contributors)
            ;
        }

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryForPublicMatchingContexts()
    {
        $queryBuilder = $this->repository->createQueryBuilder('mc')
            ->select('mc')
            ->leftJoin('mc.notice', 'notice')
            ->leftJoin('notice.contributor', 'contributor')
            ->andWhere('contributor.enabled = true')
            ->andWhere('notice.visibility=:visibility')
            ->andWhere('notice.expires >= CURRENT_TIMESTAMP() OR notice.expires IS NULL')
            ->setParameter('visibility', NoticeVisibility::PUBLIC_VISIBILITY())
        ;

        return $queryBuilder;
    }

    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return MatchingContext::class;
    }
}
