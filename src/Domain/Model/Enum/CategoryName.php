<?php

namespace Domain\Model\Enum;

use MabeEnum\Enum;

/**
 * The different category names.
 */
class CategoryName extends Enum
{
    const CONSO = 'Conso';
    const INFOS = 'Infos & média';
    const PRO = 'Professionnel';
    const MILITANT = 'Militant';
    const CULTURE = 'Culture & Loisir';

    public static function getChoices()
    {
        return array_flip(self::getConstants());
    }
}
