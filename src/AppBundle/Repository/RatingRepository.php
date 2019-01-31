<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;

class RatingRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Notice $notice
     * @return array
     */
    public function getGraphDataByNoticeType(Notice $notice, $type)
    {
        $from = new \DateTime('today');
        $from->modify('-1 month');
        $to   = new \DateTime();

        $qb = $this->createQueryBuilder('r')
            ->select('DAY(r.context.datetime) AS gDay, MONTH(r.context.datetime) AS gDmonth, YEAR(r.context.datetime) AS gDyear, COUNT(r.id) AS _count');
        $qb->andWhere($qb->expr()->eq('r.type',':type'))->setParameter('type',$type);
        $qb->andWhere($qb->expr()->eq('r.notice',':notice'))->setParameter('notice',$notice);
        $qb->andWhere($qb->expr()->isNotNull('r.context.datetime'));

        $qb ->addGroupBy('gDay')
            ->addGroupBy('gDmonth')
            ->addGroupBy('gDyear');
        $qb
            ->andWhere('r.context.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to);
        $qb->orderBy('gDyear','ASC');
        $qb->addOrderBy('gDmonth','ASC');
        $qb->addOrderBy('gDay','ASC');

        $items = $qb->getQuery()->getResult();
        $return = [];
        if(count($items)) {
            while ($from < $to) {
                $date = $from->format('Y-m-d');
                foreach ($items as $item) {
                    $dateFormatted = $item['gDyear'].'-'.sprintf("%02d",$item['gDmonth']).'-'.sprintf("%02d",$item['gDay']);
                    if($dateFormatted == $date)
                        $return[$date] = (int)$item['_count'];
                }
                if(!isset($return[$date]))
                    $return[$date] = 0;
                $from->add(new \DateInterval('P1D'));
            }
        }
        return $return;
    }
}
