<?php
/**
 * Created by PhpStorm.
 * User: insitu
 * Date: 01/03/19
 * Time: 15:20.
 */

namespace AppBundle\Service;

class DateInterval
{
    public function oneDay(): \DateInterval
    {
        return new \DateInterval('P1D');
    }
}
