<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notice;
use App\Helper\NoticeVisibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class NoticeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notice::class);
    }

    /**
     * @return Paginator<Notice>
     */
    public function getPage(int $limit, int $offset): Paginator
    {
        return self::getPaginator(
            $this->createQueryForPublicNotices(),
            $limit,
            $offset
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getOne(?int $id): ?Notice
    {
        return $this->createQueryForPublicNotices('n')
            ->andWhere('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Paginator<Notice>
     */
    public function getPageByContributor(int $contributorId, int $limit, int $offset): Paginator
    {
        return self::getPaginator(
            $this->createQueryForPublicNotices('n')
                ->andWhere('n.contributor = :contributorId')
                ->setParameter('contributorId', $contributorId),
            $limit,
            $offset
        );
    }

    private function createQueryForPublicNotices(string $noticeAlias = 'n', string $contributorAlias = 'c'): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($noticeAlias)
            ->select($noticeAlias)
            ->leftJoin("$noticeAlias.contributor", $contributorAlias)
            ->where("$contributorAlias.enabled = true")
            ->orderBy("$noticeAlias.created", 'DESC')
            ->addOrderBy("$noticeAlias.id", 'DESC');

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

    /**
     * @return Paginator<Notice>
     */
    public static function getPaginator(QueryBuilder $queryBuilder, int $limit, int $offset): Paginator
    {
        return new Paginator(
            $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit),
            true
        );
    }

    public function getClassName(): string
    {
        return Notice::class;
    }
}
