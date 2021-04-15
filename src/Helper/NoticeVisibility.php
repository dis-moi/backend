<?php

declare(strict_types=1);

namespace App\Helper;

use MabeEnum\Enum;

class NoticeVisibility extends Enum
{
    public const PUBLIC_VISIBILITY = 'public';
    public const PRIVATE_VISIBILITY = 'private';
    public const ARCHIVED_VISIBILITY = 'archived';
    public const DRAFT_VISIBILITY = 'draft';
    public const QUESTION_VISIBILITY = 'question';

    public static function getDefault(): self
    {
        return self::PUBLIC_VISIBILITY;
    }

    public function __toString()
    {
        return self::getValue();
    }

    /**
     * @return array<string, self>
     */
    public static function getChoices(): array
    {
        return array_combine(
            self::getValues(),
            self::getEnumerators(),
        );
    }
}
