<?php

namespace AppBundle\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Context
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="geolocation", type="string")
     */
    private $geolocation;

    /**
     * @param \DateTime $datetime
     * @param string $url
     * @param string $geolocation
     */
    public function __construct(\DateTime $datetime, $url, $geolocation)
    {
        $this->datetime = $datetime;
        $this->url = $url;
        $this->geolocation = $geolocation;
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

    /**
     * @return string
     */
    public function getGeolocation()
    {
        return $this->geolocation;
    }
}
