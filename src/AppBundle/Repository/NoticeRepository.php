<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Notice;
use Doctrine\ORM\QueryBuilder;

class NoticeRepository extends BaseRepository
{

    public function getOne(int $id = null) : ?Notice
    {
        $queryBuilder = $this->repository->createQueryBuilder('n')
            ->select('n,c,i')
            ->leftJoin('n.contributor', 'c')
            ->leftJoin('n.intention', 'i')
            ->where('n.id = :id')
            ->andWhere('c.enabled = true')
            ->setParameter('id', $id);

        return self::addNoticeExpirationLogic($queryBuilder)
            ->getQuery()->getOneOrNullResult()
        ;
    }

    public static function addNoticeExpirationLogic(QueryBuilder $queryBuilder, string $noticeAlias = 'n') : QueryBuilder
    {
        return $queryBuilder->andWhere(sprintf('%s.expires >= CURRENT_TIMESTAMP() OR %s.expires IS NULL OR (%s.expires <= CURRENT_TIMESTAMP() AND %s.unpublishedOnExpiration = false)',
                    $noticeAlias, $noticeAlias, $noticeAlias, $noticeAlias)
        );
    }

    public function getResourceClassName() : string
    {
        return Notice::class;
    }
}
