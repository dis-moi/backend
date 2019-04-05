<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\DateTimeImmutable;
use AppBundle\Service\DateInterval;

class RatingRepository extends BaseRepository
{
    private $dateInterval;

    private $from;
    private $to;

    public function __construct(EntityManagerInterface $entityManager, DateTimeImmutable $dateTime, DateInterval $dateInterval)
    {
        parent::__construct($entityManager);

        $this->dateInterval = $dateInterval;

        $this->from = $dateTime->threeMonthsAgo();
        $this->to = $dateTime->today();
    }

    private function getDataByNoticeTypes(Notice $notice, array $types) : array
    {
        $qb = $this->repository->createQueryBuilder('r')
            ->select('DATE_FORMAT(r.context.datetime, \'%Y-%m-%d\') AS gDate, r.type AS gType, COUNT(r.id) AS count');

        $qb ->addGroupBy('gDate')
            ->addGroupBy('gType');

        $orX = $qb->expr()->orX();
        foreach ($types as $type) {
            $param = sprintf('t_%s', preg_replace('/\W/', '', $type));
            $orX->add($qb->expr()->eq('r.type', ":$param"));
            $qb->setParameter($param, $type);
        }

        $qb ->andWhere($orX)
            ->andWhere($qb->expr()->eq('r.notice',':notice'))->setParameter('notice',$notice)
            ->andWhere($qb->expr()->isNotNull('r.context.datetime'))
            ->andWhere('r.context.datetime BETWEEN :from AND :to')
            ->setParameter('from', $this->from )
            ->setParameter('to', $this->to);

        $qb->orderBy('gDate','ASC');

        return $qb->getQuery()->getResult();
    }

    public function getGraphDataByNoticeBalanceType(Notice $notice, string $typeUp, string $typeDown) : array
    {
        $items = $this->getDataByNoticeTypes($notice, array($typeUp, $typeDown));
        return $this->extractDailyCount($items, array($typeUp));
    }

    public function getGraphDataByNoticeTypes(Notice $notice, array $types) : array
    {
        return $this->extractDailyCount(
            $this->getDataByNoticeTypes($notice, $types),
            $types
        );
    }

    private static function formatDate(\DateTimeInterface $date) {
        return $date->format('Y-m-d');
    }

    private function extractDailyCount(array $items, array $typesUp) : array
    {
        $from = $this->from;
        $to   = $this->to;

        $countsPerDate = array_reduce($items, function ($acc, $curr) use ($typesUp) {
            $date = $curr['gDate'];
            $count = (in_array($curr['gType'], $typesUp) ? +1 : -1) * $curr['count'];
            return array_merge($acc, [ $date => $count + ($acc[$date] ?? 0) ]);
        }, []);

        return $this->fillDateRange($from, $to, function (\DateTimeInterface $date) use ($countsPerDate) {
            $fdate = self::formatDate($date);
            $count = $countsPerDate[$fdate] ?? 0;
            return $count > 0 ? $count : 0;
        });

    }

    private function fillDateRange(\DateTimeImmutable $from, \DateTimeInterface $to, $fillWith, $range = []): array {
        $nextRange = array_merge($range, [ self::formatDate($from) => $fillWith($from) ]);

        return $from < $to
            ? $this->fillDateRange(
                $from->add($this->dateInterval->oneDay()),
                $to,
                $fillWith,
                $nextRange
            )
            : $nextRange;
    }

    public function getResourceClassName() : string
    {
        return Rating::class;
    }
}