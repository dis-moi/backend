<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * Class StringHelper.
 */
class StringHelper
{
    private function __construct()
    {
    }

    public static function truncate(string $string, int $length): string
    {
        if (\strlen($string) <= $length) {
            return $string;
        }

        $text = substr($string, 0, $length);

        return substr($text, 0, strrpos($text, ' ')).' …';
    }
}
