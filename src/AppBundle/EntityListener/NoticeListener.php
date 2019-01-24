<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class NoticeListener
{
    use UpdateNoticeTrait;

    public function prePersist(Notice $notice, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($notice);
    }

    public function preUpdate(Notice $notice, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($notice);
    }
}
