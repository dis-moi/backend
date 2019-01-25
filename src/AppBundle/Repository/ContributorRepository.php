<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Contributor;
use Doctrine\ORM\EntityRepository;

class ContributorRepository extends BaseRepository
{
    public function getAllEnabled()
    {
        return $this->repository->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->getQuery()->execute();
    }

    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return Contributor::class;
    }

    public static function getOrderedList(EntityRepository $er)
    {
        return $er->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');
    }

}
