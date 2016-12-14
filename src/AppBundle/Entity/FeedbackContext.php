<?php

namespace AppBundle\Entity;

class FeedbackContext
{
    private $datetime;
    private $url;

    /**
     * FeedbackContext constructor.
     *
     * @param \DateTime $datetime
     * @param  string   $url
     */
    public function __construct(\DateTime $datetime, $url)
    {
        $this->datetime = $datetime;
        $this->url = $url;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}
