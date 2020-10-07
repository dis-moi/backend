<?php

namespace App\EntityListener;

use App\Entity\MatchingContext;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class MatchingContextListener
{
    use UpdateNoticeTrait;

    public function prePersist(MatchingContext $matchingContext, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($matchingContext->getNotice());
    }

    public function preUpdate(MatchingContext $matchingContext, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($matchingContext->getNotice());
    }
}
