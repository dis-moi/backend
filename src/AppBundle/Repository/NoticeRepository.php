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
     * @throws NonUniqueResultExceptionAlias
     */
    public function getOne(int $id): ?Notice
    {
        return $this->createQueryForPublicNotices('n')
            ->andWhere('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByContributor(int $contributorId)
    {
        return $this->createQueryForPublicNotices('n')
            ->andWhere('n.contributor = :contributorId')
            ->setParameter('contributorId', $contributorId)
            ->getQuery()
            ->getResult();
    }

    private function createQueryForPublicNotices(string $noticeAlias = 'n', string $contributorAlias = 'c'): QueryBuilder
    {
        $queryBuilder = $this->repository->createQueryBuilder($noticeAlias)
            ->select("$noticeAlias, $contributorAlias")
            ->leftJoin("$noticeAlias.contributor", $contributorAlias)
            ->where("$contributorAlias.enabled = true")
            ->orderBy("$noticeAlias.created", 'DESC')
        ;

        return self::addNoticeVisibilityLogic($queryBuilder, $noticeAlias);
    }

    public static function addNoticeExpirationLogic(QueryBuilder $queryBuilder, string $noticeAlias = 'n'): QueryBuilder
    {
        return $queryBuilder
            ->andWhere("$noticeAlias.expires >= CURRENT_TIMESTAMP() OR $noticeAlias.expires IS NULL OR ($noticeAlias.expires <= CURRENT_TIMESTAMP() AND $noticeAlias.unpublishedOnExpiration = false)");
    }

    public static function addNoticeVisibilityLogic(QueryBuilder $queryBuilder, string $noticeAlias = 'n'): QueryBuilder
    {
        return self::addNoticeExpirationLogic($queryBuilder, $noticeAlias)
            ->andWhere("$noticeAlias.visibility=:visibility")
            ->setParameter('visibility', NoticeVisibility::PUBLIC_VISIBILITY());
    }

    public function getResourceClassName(): string
    {
        return Notice::class;
    }
}
