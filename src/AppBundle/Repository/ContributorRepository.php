<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Contributor;

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
}
