<?php

declare(strict_types=1);

namespace App\Service;

class DateInterval
{
    public function oneDay(): \DateInterval
    {
        return new \DateInterval('P1D');
    }
}
