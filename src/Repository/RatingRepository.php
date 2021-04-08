<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notice;
use App\Entity\Rating;
use App\Service\DateInterval;
use App\Service\DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RatingRepository extends ServiceEntityRepository
{
    /**
     * @var DateInterval
     */
    private $dateInterval;

    /**
     * @var \DateTimeImmutable
     */
    private $from;

    /**
     * @var \DateTimeImmutable
     */
    private $to;

    public function __construct(ManagerRegistry $registry, DateTimeImmutable $dateTime, DateInterval $dateInterval)
    {
        parent::__construct($registry, Rating::class);

        $this->dateInterval = $dateInterval;

        $this->from = $dateTime->threeMonthsAgo();
        $this->to = $dateTime->today();
    }

    /**
     * @param string[] $types
     *
     * @return array<string, mixed>
     */
    private function getDataByNoticeTypes(Notice $notice, array $types): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('DATE_FORMAT(r.context.datetime, \'%Y-%m-%d\') AS gDate, r.type AS gType, COUNT(r.id) AS count');

        $qb->addGroupBy('gDate')
            ->addGroupBy('gType');

        $orX = $qb->expr()->orX();
        foreach ($types as $type) {
            $param = sprintf('t_%s', preg_replace('/\W/', '', $type));
            $orX->add($qb->expr()->eq('r.type', ":$param"));
            $qb->setParameter($param, $type);
        }

        $qb->andWhere($orX)
            ->andWhere($qb->expr()->eq('r.notice', ':notice'))->setParameter('notice', $notice)
            ->andWhere($qb->expr()->isNotNull('r.context.datetime'))
            ->andWhere('r.context.datetime BETWEEN :from AND :to')
            ->setParameter('from', $this->from)
            ->setParameter('to', $this->to);

        $qb->orderBy('gDate', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int[]
     */
    public function getGraphDataByNoticeBalanceType(Notice $notice, string $typeUp, string $typeDown): array
    {
        $items = $this->getDataByNoticeTypes($notice, [$typeUp, $typeDown]);

        return $this->extractDailyCount($items, [$typeUp]);
    }

    /**
     * @param string[] $types
     *
     * @return int[]
     */
    public function getGraphDataByNoticeTypes(Notice $notice, array $types): array
    {
        return $this->extractDailyCount(
            $this->getDataByNoticeTypes($notice, $types),
            $types
        );
    }

    private static function formatDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    /**
     * @param array<string, mixed> $items
     * @param string[]             $typesUp
     *
     * @return int[]
     */
    private function extractDailyCount(array $items, array $typesUp): array
    {
        $from = $this->from;
        $to = $this->to;

        $countsPerDate = array_reduce($items, function ($acc, $curr) use ($typesUp) {
            $date = $curr['gDate'];
            $count = (\in_array($curr['gType'], $typesUp, true) ? +1 : -1) * $curr['count'];

            return array_merge($acc, [$date => $count + ($acc[$date] ?? 0)]);
        }, []);

        return $this->fillDateRange($from, $to, function (\DateTimeInterface $date) use ($countsPerDate) {
            $fdate = self::formatDate($date);
            $count = $countsPerDate[$fdate] ?? 0;

            return $count > 0 ? $count : 0;
        });
    }

    /**
     * @param mixed[] $range
     *
     * @return mixed[]
     */
    private function fillDateRange(\DateTimeImmutable $from, \DateTimeInterface $to, callable $fillWith, array $range = []): array
    {
        $nextRange = array_merge($range, [self::formatDate($from) => $fillWith($from)]);

        return $from < $to
            ? $this->fillDateRange(
                $from->add($this->dateInterval->oneDay()),
                $to,
                $fillWith,
                $nextRange
            )
            : $nextRange;
    }
}
