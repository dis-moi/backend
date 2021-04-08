<?php

declare(strict_types=1);

namespace App\Service;

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
