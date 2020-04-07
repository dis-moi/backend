<?php

namespace AppBundle\Helper;

use MabeEnum\Enum;

class NoticeVisibility extends Enum
{
    const PUBLIC_VISIBILITY = 'public';
    const PRIVATE_VISIBILITY = 'private'; // draft
    const ARCHIVED_VISIBILITY = 'archived';

    public static function getDefault()
    {
        return self::PRIVATE_VISIBILITY();
    }

    public function __toString()
    {
        return self::getValue();
    }
}
