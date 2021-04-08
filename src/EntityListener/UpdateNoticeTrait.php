<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Notice;

trait UpdateNoticeTrait
{
    protected function updateNotice(Notice $notice): void
    {
        $notice->setUpdated(new \DateTime());
    }
}
