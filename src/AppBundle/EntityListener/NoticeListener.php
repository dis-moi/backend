<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class NoticeListener
{
    use UpdateNoticeTrait;
    use CreateNoticeTrait;

    public function prePersist(Notice $notice, LifecycleEventArgs $event = null)
    {
        $this->createNotice($notice);
    }

    public function preUpdate(Notice $notice, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($notice);
    }
}
