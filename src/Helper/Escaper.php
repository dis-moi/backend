<?php

namespace App\Helper;

interface Escaper
{
    public static function escape(string $input): string;
}
