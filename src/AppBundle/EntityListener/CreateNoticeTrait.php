<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;

trait CreateNoticeTrait
{
    protected function createNotice(Notice $notice)
    {
        $notice->setCreated(new \DateTime());
    }
}
