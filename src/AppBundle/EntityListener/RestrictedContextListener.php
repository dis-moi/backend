<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\RestrictedContext;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class RestrictedContextListener
{
    use UpdateNoticeTrait;

    public function prePersist(RestrictedContext $restrictedContext, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($restrictedContext->getNotice());
    }

    public function preUpdate(RestrictedContext $restrictedContext, LifecycleEventArgs $event = null)
    {
        $this->updateNotice($restrictedContext->getNotice());
    }
}
