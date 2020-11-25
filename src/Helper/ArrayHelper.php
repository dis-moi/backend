<?php

declare(strict_types=1);

namespace App\Helper;


/**
 * Class ArrayHelper.
 */
class ArrayHelper
{
    private function __construct()
    {
    }

    public static function find(array $collection, callable $predicate)
    {
        return current(
            array_filter(
                $collection,
                $predicate
            )
        );
    }
}
