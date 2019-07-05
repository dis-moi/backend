<?php
/**
 * Created by PhpStorm.
 * User: insitu
 * Date: 01/03/19
 * Time: 15:10
 */

namespace AppBundle\Service;


class DateTimeImmutable
{

    public function today(): \DateTimeImmutable {
        return new \DateTimeImmutable('today');
    }

    public function threeMonthsAgo(): \DateTimeImmutable {
        return new \DateTimeImmutable('-3 months');
    }

    public function oneYearAhead(): \DateTimeImmutable {
        return new \DateTimeImmutable('+1 year');
    }

}