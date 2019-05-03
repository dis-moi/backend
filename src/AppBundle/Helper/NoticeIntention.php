<?php

namespace AppBundle\Helper;

use MabeEnum\Enum;

class NoticeIntention extends Enum {
    const APPROVAL = 'approval';
    const UNAPPROVAL = 'unapproval';
    const ALTERNATIVE = 'alternative';
    const INFORMATION = 'information';
    const OTHER = 'other';

    static function getDefault(){
        return self::OTHER();
    }

    public function __toString(){
        return self::getValue();
    }
}
