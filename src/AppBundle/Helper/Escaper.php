<?php

namespace AppBundle\Helper;

interface Escaper
{
    public static function escape(string $input): string;
}
