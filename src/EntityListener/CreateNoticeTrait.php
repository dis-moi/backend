<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Notice;
use DateTime as DateTime;

trait CreateNoticeTrait
{
    protected function createNotice(Notice $notice): void
    {
        $notice->setCreated(new DateTime());
    }
}
