<?php

declare(strict_types=1);

namespace App\Helper;

class PregEscaper implements Escaper
{
    public static function escape(string $input): string
    {
        return preg_quote($input);
    }
}
