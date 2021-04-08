<?php

declare(strict_types=1);

namespace App\Helper;

interface Escaper
{
    public static function escape(string $input): string;
}
