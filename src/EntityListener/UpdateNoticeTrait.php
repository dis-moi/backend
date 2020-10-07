<?php

namespace App\EntityListener;

use App\Entity\Notice;

trait UpdateNoticeTrait
{
    protected function updateNotice(Notice $notice)
    {
        $notice->setUpdated(new \DateTime());
    }
}
