<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\MatchingContext;
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
