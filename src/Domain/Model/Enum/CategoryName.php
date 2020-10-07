<?php

namespace App\Domain\Model\Enum;

use MabeEnum\Enum;

/**
 * The different category names.
 */
class CategoryName extends Enum
{
    const CONSO = 'Conso';
    const CULTURE = 'Culture & Société';
    const MILITANT = 'Militant';
    const DIVERS = 'Divers';

    public static function getChoices()
    {
        return array_flip(self::getConstants());
    }
}
