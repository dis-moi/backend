<?php

namespace App\EntityListener;

use App\Entity\Notice;
use DateTime as DateTime;

trait CreateNoticeTrait
{
    protected function createNotice(Notice $notice)
    {
        $notice->setCreated(new DateTime());
    }
}
