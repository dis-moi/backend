<?php

namespace AppBundle\Helper;

use MabeEnum\Enum;

class ContributorSubscription extends Enum {
    const SUBSCRIBE = 'subscribe';
    const UNSUBSCRIBE = 'unsubscribe';

    public function __toString(){
        return self::getValue();
    }
}
