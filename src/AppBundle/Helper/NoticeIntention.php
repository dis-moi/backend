<?php

namespace AppBundle\Helper;

use MabeEnum\Enum;

class NoticeIntention extends Enum
{
    const APPROVAL = 'approval';
    const DISAPPROVAL = 'disapproval';
    const ALTERNATIVE = 'alternative';
    const INFORMATION = 'information';
    const OTHER = 'other';

    public static function getDefault()
    {
        return self::OTHER();
    }

    public function __toString()
    {
        return self::getValue();
    }
}
