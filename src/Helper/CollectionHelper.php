<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\Common\Collections\Collection;

/**
 * Class CollectionHelper.
 */
class CollectionHelper
{
    private function __construct()
    {
    }

    /**
     * @return mixed
     */
    public static function find(Collection $collection, callable $predicate)
    {
        return $collection->filter($predicate)->first();
    }
}
