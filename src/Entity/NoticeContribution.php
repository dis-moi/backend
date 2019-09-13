<?php

namespace AppBundle\Entity;

use AppBundle\Helper\NoticeIntention;
use Symfony\Component\Validator\Constraints as Assert;

class NoticeContribution
{

    /**
     * @var string
     *
     * @Assert\Url
     */
    private $url;

    /**
     * @var string
     */
    private $contributorName;

    /**
     * @var string
     *
     * @Assert\Email
     */
    private $contributorEmail;

    /**
     * @var string
     */
    private $message;

    /**
     * @var NoticeIntention
     *
     * @Assert\Type(NoticeIntention)
     */
    private $intention;

    function __construct($contributorName, $contributorEmail, $url, $intention, $message) {
        $this->contributorName = $contributorName;
        $this->contributorEmail = $contributorEmail;
        $this->url = $url;
        $this->intention = $intention;
        $this->message = $message;
    }

    public function getContributorName(): string
    {
        return $this->contributorName;
    }

    public function getContributorEmail(): string
    {
        return $this->contributorEmail;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getIntention(): NoticeIntention
    {
        return $this->intention;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function __toString() : string
    {
        return "Contribution of $this->contributorName on $this->url";
    }
}
