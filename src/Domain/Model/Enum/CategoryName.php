<?php

declare(strict_types=1);

namespace App\Domain\Model\Enum;

use MabeEnum\Enum;

/**
 * The different category names.
 */
class CategoryName extends Enum
{
    public const CONSO = 'Conso';
    public const CULTURE = 'Culture & Société';
    public const MILITANT = 'Militant';
    public const DIVERS = 'Divers';

    /**
     * @return array<string, string>
     */
    public static function getChoices(): array
    {
        return array_flip(self::getConstants());
    }
}
