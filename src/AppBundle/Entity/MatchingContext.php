<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MatchingContext
 *
 * @ORM\Table(name="matching_context")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatchingContextRepository")
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
     * @ORM\ManyToOne(targetEntity="Recommendation", inversedBy="matchingContexts", fetch="EAGER")
     */
    private $recommendation;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
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
     * Set recommendation
     *
     * @param Recommendation $recommendation
     *
     * @return MatchingContext
     */
    public function setRecommendation(Recommendation $recommendation = null)
    {
        $this->recommendation = $recommendation;

        return $this;
    }

    /**
     * Get recommendation
     *
     * @return Recommendation
     */
    public function getRecommendation()
    {
        return $this->recommendation;
    }

    public function __toString()
    {
        return (is_null($this->getDescription())) ? 'you must set a description' : $this->getDescription();
    }
}
