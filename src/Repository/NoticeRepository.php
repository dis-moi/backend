<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notice;
use App\Entity\Rating;
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

    private static function addRatingsCount(QueryBuilder $queryBuilder, string $noticeAlias = 'n'): QueryBuilder
    {
//        $countQuery = $this->createQueryBuilder('r')
//            ->select('COUNT(r.id)')
//            ->from('')

        return $queryBuilder
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::BADGE."' THEN 1 ELSE 0 END )) as badgedCount")
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::DISPLAY."' THEN 1 ELSE 0 END )) as displayedCount")
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::UNFOLD."' THEN 1 ELSE 0 END )) as unfoldedCount")
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::OUTBOUND_CLICK."' THEN 1 ELSE 0 END )) as clickedCount")
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::LIKE."' THEN 1 ELSE 0 END )) as likedCount")
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::DISLIKE."' THEN 1 ELSE 0 END )) as dislikedCount")
            ->addSelect("(SUM (CASE WHEN r.type = '".Rating::DISMISS."' THEN 1 ELSE 0 END )) as dismissedCount")
            ->leftJoin('n.ratings', 'r')
            ->groupBy('n.id');
    }

    /*
     * create index rating_id_type_index
    on rating (id, type);

     */

    /**
     * @param mixed[]|null $result
     */
    private static function mergeRatingsCountWithNotice(?array $result): ?Notice
    {
        if ( ! $result || ! $result[0]) {
            return null;
        }

        /** @var Notice $notice */
        $notice = $result[0];
        $notice
            ->setBadgedRatingCount((int) $result['badgedCount'])
            ->setDisplayedRatingCount((int) $result['displayedCount'])
            ->setUnfoldedRatingCount((int) $result['unfoldedCount'])
            ->setClickedRatingCount((int) $result['clickedCount'])
            ->setLikedRatingCount((int) $result['likedCount'])
            ->setDislikedRatingCount((int) $result['dislikedCount'])
            ->setDismissedRatingCount((int) $result['dismissedCount']);

        return $notice;
    }

    /**
     * @return Notice[]
     */
    public function getAll(?string $dqlFilter): array
    {
        $mainQuery = $this->createQueryBuilder('n');
        $mainQuery = self::addRatingsCount($mainQuery);

        if ($dqlFilter) {
            $mainQuery = $mainQuery->andWhere($dqlFilter);
        }

        $resultsWithRatingsCount = $mainQuery->getQuery()->getResult();

        return array_map('self::mergeRatingsCountWithNotice', $resultsWithRatingsCount);
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
