<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;
use \DateTime as DateTime;
use AppBundle\Service\DateTimeImmutable;

trait CreateNoticeTrait
{
    protected function createNotice(Notice $notice)
    {
        $notice->setCreated(new DateTime());

        $notice->setInitialExpires(new DateTimeImmutable());
    }
}
