<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Notice;

class NoticeRepository extends BaseRepository
{
    /**
     * @param $id
     * @return Notice|null
     */
    public function getOne($id)
    {
        return $this->repository->createQueryBuilder('n')
            ->select('n,c,t')
            ->leftJoin('n.contributor', 'c')
            ->leftJoin('n.type', 't')
            ->where('n.id = :id')
            ->andWhere('c.enabled = true')
            ->setParameter('id', $id)
        ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return Notice::class;
    }
}
