<?php

namespace AppBundle\Repository;

use AppBundle\Entity\RestrictedContext;

class RestrictedContextRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return RestrictedContext::class;
    }
}
