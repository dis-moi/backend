<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Context
{
    public const CONTEXT_URL_MAX_LENGTH = 1000;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=Context::CONTEXT_URL_MAX_LENGTH, nullable=true)
     * @Assert\Url
     * @Assert\Length(max=Context::CONTEXT_URL_MAX_LENGTH)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="geolocation", type="string", nullable=true)
     */
    private $geolocation;

    /**
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
