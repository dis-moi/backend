<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Notice;
use AppBundle\Helper\NoticeVisibility;
use Doctrine\ORM\QueryBuilder;

class NoticeRepository extends BaseRepository
{
    public function getAll()
    {
        return $this->createQueryForPublicNotices()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int|null $id
     *
     * @return Notice|null
     *
     * @throws NonUniqueResultExceptionAlias
     */
    public function getOne($id)
    {
        return $this->createQueryForPublicNotices()
            ->andWhere('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByContributor($contributorId)
    {
        return $this->createQueryForPublicNotices()
            ->where('n.contributor = :contributorId')
            ->setParameter('contributorId', $contributorId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $noticeAlias
     *
     * @return QueryBuilder
     */
    private function createQueryForPublicNotices()
    {
        $queryBuilder = $this->repository->createQueryBuilder('n')
            ->select('n,c')
            ->leftJoin('n.contributor', 'c')
            ->where('c.enabled = true');

        return self::addNoticeVisibilityLogic($queryBuilder);
    }

    /**
     * @param string $noticeAlias
     *
     * @return QueryBuilder
     */
    public static function addNoticeExpirationLogic(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->andWhere('n.expires >= CURRENT_TIMESTAMP() OR n.expires IS NULL OR (n.expires <= CURRENT_TIMESTAMP() AND n.unpublishedOnExpiration = false)');
    }

    public static function addNoticeVisibilityLogic(QueryBuilder $queryBuilder)
    {
        return self::addNoticeExpirationLogic($queryBuilder)
            ->andWhere('n.visibility=:visibility')
            ->setParameter('visibility', NoticeVisibility::PUBLIC_VISIBILITY());
    }

    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return Notice::class;
    }
}
