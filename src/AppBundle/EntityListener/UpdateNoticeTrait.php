<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;

trait UpdateNoticeTrait
{
    protected function updateNotice(Notice $notice)
    {
        $notice->setUpdated(new \DateTime());
    }
}
