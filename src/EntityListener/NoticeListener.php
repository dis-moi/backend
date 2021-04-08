<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Notice;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class NoticeListener
{
    use UpdateNoticeTrait;
    use CreateNoticeTrait;

    public function prePersist(Notice $notice, LifecycleEventArgs $event = null): void
    {
        $this->createNotice($notice);
    }

    public function preUpdate(Notice $notice, LifecycleEventArgs $event = null): void
    {
        $this->updateNotice($notice);
    }
}
