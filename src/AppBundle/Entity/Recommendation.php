<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\OneToMany(targetEntity="Alternative", mappedBy="recommendation", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @Assert\Valid
     */
    private $alternatives;

    /**
     * @ORM\OneToMany(targetEntity="MatchingContext", mappedBy="recommendation", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid
     */
    private $matchingContexts;

    /**
     * @ORM\ManyToMany(targetEntity="Criterion", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $criteria;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity="Resource", mappedBy="recommendation", cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Contributor", inversedBy="recommendations", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
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
        $this->description = strip_tags($description);

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
        $this->criteria = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set Resource
     *
     * @param Resource $resource
     *
     * @return Recommendation
     */
    public function setResource(Resource $resource = null)
    {
        $resource->setRecommendation($this);

        $this->resource = $resource;

        return $this;
    }

    /**
     * Get Resource
     *
     * @return \AppBundle\Entity\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Add criterion
     *
     * @param \AppBundle\Entity\Criterion $criterion
     *
     * @return Recommendation
     */
    public function addCriterion(\AppBundle\Entity\Criterion $criterion)
    {
        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * Remove criterion
     *
     * @param \AppBundle\Entity\Criterion $criterion
     */
    public function removeCriterion(\AppBundle\Entity\Criterion $criterion)
    {
        $this->criteria->removeElement($criterion);
    }

    /**
     * Get criteria
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @return string
     */
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
        if (!$this->visibility) {
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

    /**
     * @return bool
     */
    public function hasPublicVisibility()
    {
        return $this->getVisibility() === RecommendationVisibility::PUBLIC_VISIBILITY();
    }
}
