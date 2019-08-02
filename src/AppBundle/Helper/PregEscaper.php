<?php


namespace AppBundle\Helper;


class PregEscaper implements Escaper
{
    public static function escape(string $input): string
    {
        return preg_quote($input);
    }
}