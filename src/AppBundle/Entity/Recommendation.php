<?php

namespace AppBundle\Entity;

use AppBundle\Entity\RecommendationVisibility;
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
     * @ORM\Column(name="visibility", type="string", options={"default" : "private"})
     */
    private $visibility;

    /**
     * @ORM\OneToMany(targetEntity="Alternative", mappedBy="recommendation", cascade={"persist", "remove"})
     */
    private $alternatives;

    /**
     * @ORM\OneToMany(targetEntity="MatchingContext", mappedBy="recommendation", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $matchingContexts;

    /**
     * @ORM\ManyToMany(targetEntity="Filter", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $filters;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity="Source", mappedBy="recommendation", cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="Contributor", inversedBy="recommendations", cascade={"persist"}, fetch="EAGER")
     */
    private $contributor;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
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
     * Constructor
     */
    public function __construct()
    {
        $this->alternatives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->matchingContexts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->filters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->visibility = RecommendationVisibility::getDefault();
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
        $alternative->setRecommendation($this);

        $this->alternatives[] = $alternative;

        return $this;
    }

    /**
     * Remove alternative
     *
     * @param \AppBundle\Entity\Alternative $alternative
     */
    public function removeAlternative(\AppBundle\Entity\Alternative $alternative)
    {
        $this->alternatives->removeElement($alternative);
    }

    /**
     * Get alternative
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlternatives()
    {
        return $this->alternatives;
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
        $matchingContext->setRecommendation($this);

        $this->matchingContexts[] = $matchingContext;

        return $this;
    }

    /**
     * Remove matchingContext
     *
     * @param \AppBundle\Entity\MatchingContext $matchingContext
     */
    public function removeMatchingContext(\AppBundle\Entity\MatchingContext $matchingContext)
    {
        $this->matchingContexts->removeElement($matchingContext);
    }

    /**
     * Get matchingContext
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatchingContexts()
    {
        return $this->matchingContexts;
    }

    /**
     * Set source
     *
     * @param \AppBundle\Entity\Source $source
     *
     * @return Recommendation
     */
    public function setSource(\AppBundle\Entity\Source $source = null)
    {
        $source->setRecommendation($this);

        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \AppBundle\Entity\Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Add filter
     *
     * @param \AppBundle\Entity\Filter $filter
     *
     * @return Recommendation
     */
    public function addFilter(\AppBundle\Entity\Filter $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Remove filter
     *
     * @param \AppBundle\Entity\Filter $filter
     */
    public function removeFilter(\AppBundle\Entity\Filter $filter)
    {
        $this->filters->removeElement($filter);
    }

    /**
     * Get filters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Set contributor
     *
     * @param \AppBundle\Entity\Contributor $contributor
     *
     * @return Recommendation
     */
    public function setContributor(\AppBundle\Entity\Contributor $contributor = null)
    {
        $this->contributor = $contributor;

        return $this;
    }

    /**
     * Get contributor
     *
     * @return \AppBundle\Entity\Contributor
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * @return RecommendationVisibility
     */
    public function getVisibility()
    {
        if(!$this->visibility){
            return RecommendationVisibility::getDefault();
        }
        return RecommendationVisibility::get($this->visibility);
    }

    /**
     * @param RecommendationVisibility $visibility
     * @throw InvalidArgumentException
     * @return Recommendation
     */
    public function setVisibility(RecommendationVisibility $visibility)
    {
        $this->visibility = $visibility->getValue();

        return $this;
    }

    public function hasPublicVisibility(){
        return $this->getVisibility() === RecommendationVisibility::PUBLIC_VISIBILITY();
    }
}
