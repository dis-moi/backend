<?php

namespace AppBundle\Helper;

use MabeEnum\Enum;

class NoticeVisibility extends Enum {
    const PUBLIC_VISIBILITY = 'public';
    const PRIVATE_VISIBILITY = 'private';

    static function getDefault(){
        /**
         * How this is working ? o.o
         */
        return self::PRIVATE_VISIBILITY();
    }

    public function __toString(){
        return self::getValue();
    }
}
