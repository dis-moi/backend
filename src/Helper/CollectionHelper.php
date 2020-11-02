<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

/**
 * Class CollectionHelper.
 */
class CollectionHelper
{
    private function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function sort(Collection $collection, callable $comparator): ArrayCollection
    {
        $array = $collection->toArray();
        uasort($array, $comparator);

        return new ArrayCollection($array);
    }

    public static function find(Collection $collection, callable $predicate)
    {
        return $collection->filter($predicate)->first();
    }
}
