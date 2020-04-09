<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Notice;
use AppBundle\Service\DateTimeImmutable;
use DateTime as DateTime;

trait CreateNoticeTrait
{
    protected function createNotice(Notice $notice)
    {
        $notice->setCreated(new DateTime());

        $notice->setInitialExpires(new DateTimeImmutable());
    }
}
