<?php

namespace AppBundle\Repository;

use AppBundle\Entity\RestrictedContext;

class RestrictedContextRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return RestrictedContext::class;
    }
}
