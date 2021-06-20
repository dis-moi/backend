<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Notice;

class NoticeRated
{
    /**
     * @var Notice
     */
    private $notice;

    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    public function getNotice(): Notice
    {
        return $this->notice;
    }
}
