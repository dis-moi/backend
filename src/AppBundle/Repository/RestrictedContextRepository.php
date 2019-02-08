<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\RestrictedContext;
use AppBundle\Helper\NoticeVisibility;
use Doctrine\ORM\QueryBuilder;

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
