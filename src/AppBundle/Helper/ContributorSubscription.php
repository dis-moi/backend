<?php

namespace AppBundle\Helper;

use MabeEnum\Enum;

class ContributorSubscription extends Enum {
    const SUBSCRIBE = 'subscribe';
    const UNSUBSCRIBE = 'unsubscribe';

//    static function getDefault(){
//        /**
//         * How this is working ? o.o
//         */
//        return self::SUBSCRIBE();
//    }

    public function __toString(){
        return self::getValue();
    }
}
