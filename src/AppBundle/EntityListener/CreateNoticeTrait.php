<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;
use DateTime as DateTime;

trait CreateNoticeTrait
{
    protected function createNotice(Notice $notice)
    {
        $notice->setCreated(new DateTime());
    }
}
