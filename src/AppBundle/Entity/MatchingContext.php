<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Escaper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\EntityListener\MatchingContextListener;

/**
 * MatchingContext
 *
 * @ORM\Table(name="matching_context")
 * @ORM\Entity
 * @ORM\EntityListeners({MatchingContextListener::class})
 */
class MatchingContext
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Notice
     *
     * @ORM\ManyToOne(targetEntity="Notice", inversedBy="matchingContexts", fetch="EAGER")
     */
    private $notice;

    /**
     * @var string
     *
     * @ORM\Column(name="exampleUrl", type="string", length=255, nullable=true)
     *
     * @Assert\Url
     */
    private $exampleUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="domainName", type="string", length=150, nullable=true)
     *
     * @Assert\Regex("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/")
     */
    private $domainName;

    /**
     * @var string
     *
     * @ORM\Column(name="urlRegex", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $urlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="excludeUrlRegex", type="string", length=255, nullable=true)
     */
    private $excludeUrlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="querySelector", type="string", length=255, nullable=true)
     */
    private $querySelector;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setExampleUrl(string $exampleUrl = null) : MatchingContext
    {
        $this->exampleUrl = $exampleUrl;

        return $this;
    }

    public function getExampleUrl() : ?string
    {
        return $this->exampleUrl;
    }

    public function setDomainName(string $domainName = null) : MatchingContext
    {
        $this->domainName = $domainName;

        return $this;
    }

    public function getDomainName() : ?string
    {
        return $this->domainName;
    }

    /**`
     * Set urlRegex
     *
     * @param string $urlRegex
     *
     * @return MatchingContext
     */
    public function setUrlRegex($urlRegex)
    {
        $this->urlRegex = $urlRegex;

        return $this;
    }

    /**
     * Get urlRegex
     *
     * @return string
     */
    public function getUrlRegex()
    {
        return $this->urlRegex;
    }

    public function getFullUrlRegex(Escaper $escaper = null) : string
    {
        if (empty($this->domainName)) {
            return $this->urlRegex;
        }
        return (is_null($escaper) ? $this->domainName : $escaper::escape($this->domainName)) . $this->urlRegex;
    }

    /**
     * @param null|string $excludeUrlRegex
     *
     * @return MatchingContext
     */
    public function setExcludeUrlRegex($excludeUrlRegex)
    {
        $this->excludeUrlRegex = $excludeUrlRegex;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExcludeUrlRegex()
    {
        return $this->excludeUrlRegex;
    }

    /**
     * Set description
     *
     * @param null|string $description
     *
     * @return MatchingContext
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notice
     *
     * @param Notice $notice
     *
     * @return MatchingContext
     */
    public function setNotice(Notice $notice = null)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return Notice
     */
    public function getNotice()
    {
        return $this->notice;
    }

    public function __toString()
    {
        return (is_null($this->getDescription())) ? 'you must set a description' : $this->getDescription();
    }

    /**
     * @return string
     */
    public function getQuerySelector()
    {
        return $this->querySelector;
    }

    /**
     * @param string $querySelector
     */
    public function setQuerySelector($querySelector)
    {
        $this->querySelector = $querySelector;
    }
}
