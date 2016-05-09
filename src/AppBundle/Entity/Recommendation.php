<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recommendation
 *
 * @ORM\Table(name="recommendation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecommendationRepository")
 */
class Recommendation
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
     * @ORM\OneToMany(targetEntity="Alternative", mappedBy="recommendation")
     */
    private $alternative;

    /**
     * @ORM\OneToMany(targetEntity="MatchingContext", mappedBy="recommendation")
     */
    private $matchingContext;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceUrl", type="string", length=255)
     */
    private $sourceUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceDescription", type="string", length=255, nullable=true)
     */
    private $sourceDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceLabel", type="string", length=255)
     */
    private $sourceLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceType", type="string", length=255)
     */
    private $sourceType;


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
     * Set title
     *
     * @param string $title
     *
     * @return Recommendation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sourceUrl
     *
     * @param string $sourceUrl
     *
     * @return Recommendation
     */
    public function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;

        return $this;
    }

    /**
     * Get sourceUrl
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * Set sourceDescription
     *
     * @param string $sourceDescription
     *
     * @return Recommendation
     */
    public function setSourceDescription($sourceDescription)
    {
        $this->sourceDescription = $sourceDescription;

        return $this;
    }

    /**
     * Get sourceDescription
     *
     * @return string
     */
    public function getSourceDescription()
    {
        return $this->sourceDescription;
    }

    /**
     * Set sourceLabel
     *
     * @param string $sourceLabel
     *
     * @return Recommendation
     */
    public function setSourceLabel($sourceLabel)
    {
        $this->sourceLabel = $sourceLabel;

        return $this;
    }

    /**
     * Get sourceLabel
     *
     * @return string
     */
    public function getSourceLabel()
    {
        return $this->sourceLabel;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Recommendation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sourceType
     *
     * @param string $sourceType
     *
     * @return Recommendation
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;

        return $this;
    }

    /**
     * Get sourceType
     *
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alternative = new \Doctrine\Common\Collections\ArrayCollection();
        $this->matchingContext = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add alternative
     *
     * @param \AppBundle\Entity\Alternative $alternative
     *
     * @return Recommendation
     */
    public function addAlternative(\AppBundle\Entity\Alternative $alternative)
    {
        $this->alternative[] = $alternative;

        return $this;
    }

    /**
     * Remove alternative
     *
     * @param \AppBundle\Entity\Alternative $alternative
     */
    public function removeAlternative(\AppBundle\Entity\Alternative $alternative)
    {
        $this->alternative->removeElement($alternative);
    }

    /**
     * Get alternative
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlternative()
    {
        return $this->alternative;
    }

    /**
     * Add matchingContext
     *
     * @param \AppBundle\Entity\MatchingContext $matchingContext
     *
     * @return Recommendation
     */
    public function addMatchingContext(\AppBundle\Entity\MatchingContext $matchingContext)
    {
        $this->matchingContext[] = $matchingContext;

        return $this;
    }

    /**
     * Remove matchingContext
     *
     * @param \AppBundle\Entity\MatchingContext $matchingContext
     */
    public function removeMatchingContext(\AppBundle\Entity\MatchingContext $matchingContext)
    {
        $this->matchingContext->removeElement($matchingContext);
    }

    /**
     * Get matchingContext
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatchingContext()
    {
        return $this->matchingContext;
    }
}
