<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Notice;
use Doctrine\ORM\QueryBuilder;

class NoticeRepository extends BaseRepository
{
    /**
     * @param int|null $id
     *
     * @return Notice|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOne($id)
    {
        return $this->createQueryForPublicNotices()
            ->where('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAll()
    {
        return $this->createQueryForPublicNotices()
            ->getQuery()
            ->getResult();
    }

    public function getByContributor($contributorId)
    {
        return $this->createQueryForPublicNotices()
            ->where('n.contributor = :contributorId')
            ->setParameter('contributorId', $contributorId)
            ->getQuery()
            ->getResult();
    }

    private function createQueryForPublicNotices()
    {
        $queryBuilder = $this->repository->createQueryBuilder('n')
            ->select('n,c')
            ->leftJoin('n.contributor', 'c')
            ->andWhere('c.enabled = true');

        return self::addNoticeExpirationLogic($queryBuilder);
    }

    /**
     * @param string $noticeAlias
     *
     * @return QueryBuilder
     */
    public static function addNoticeExpirationLogic(QueryBuilder $queryBuilder, $noticeAlias = 'n')
    {
        return $queryBuilder->andWhere(sprintf('%s.expires >= CURRENT_TIMESTAMP() OR %s.expires IS NULL OR (%s.expires <= CURRENT_TIMESTAMP() AND %s.unpublishedOnExpiration = false)',
                    $noticeAlias, $noticeAlias, $noticeAlias, $noticeAlias)
        );
    }

    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return Notice::class;
    }
}
