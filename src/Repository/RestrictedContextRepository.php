<?php

namespace App\Repository;

use App\Entity\RestrictedContext;

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
