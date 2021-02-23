<?php

namespace App\Helper;

use MabeEnum\Enum;

class NoticeVisibility extends Enum
{
    const PUBLIC_VISIBILITY = 'public';
    const PRIVATE_VISIBILITY = 'private';
    const ARCHIVED_VISIBILITY = 'archived';

    public static function getDefault()
    {
        return self::PUBLIC_VISIBILITY();
    }

    public function __toString()
    {
        return self::getValue();
    }

    public static function getChoices(): array
    {
        return array_combine(
            NoticeVisibility::getValues(),
            NoticeVisibility::getEnumerators(),
        );
    }
}
