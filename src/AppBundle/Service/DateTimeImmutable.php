<?php
/**
 * Created by PhpStorm.
 * User: insitu
 * Date: 01/03/19
 * Time: 15:10.
 */

namespace AppBundle\Service;

class DateTimeImmutable
{
    public function today(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('+1 day'); // Include today
    }

    public function threeMonthsAgo(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('-3 months');
    }
}
