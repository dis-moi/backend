<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToMany(targetEntity="Channel", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channels;

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
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="recommendation", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     */
    private $feedbacks;

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
        $this->channels = new ArrayCollection();
        $this->alternatives = new ArrayCollection();
        $this->matchingContexts = new ArrayCollection();
        $this->criteria = new ArrayCollection();
        $this->visibility = RecommendationVisibility::getDefault();
    }

    /**
     * Add alternative
     *
     * @param Alternative $alternative
     *
     * @return Recommendation
     */
    public function addAlternative(Alternative $alternative)
    {
        $alternative->setRecommendation($this);

        $this->alternatives[] = $alternative;

        return $this;
    }

    /**
     * Remove alternative
     *
     * @param Alternative $alternative
     */
    public function removeAlternative(Alternative $alternative)
    {
        $this->alternatives->removeElement($alternative);
    }

    /**
     * Get alternative
     *
     * @return Collection
     */
    public function getAlternatives()
    {
        return $this->alternatives;
    }

    /**
     * Add matchingContext
     *
     * @param MatchingContext $matchingContext
     *
     * @return Recommendation
     */
    public function addMatchingContext(MatchingContext $matchingContext)
    {
        $matchingContext->setRecommendation($this);

        $this->matchingContexts[] = $matchingContext;

        return $this;
    }

    /**
     * Remove matchingContext
     *
     * @param MatchingContext $matchingContext
     */
    public function removeMatchingContext(MatchingContext $matchingContext)
    {
        $this->matchingContexts->removeElement($matchingContext);
    }

    /**
     * Get matchingContext
     *
     * @return Collection
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
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Add criterion
     *
     * @param Criterion $criterion
     *
     * @return Recommendation
     */
    public function addCriterion(Criterion $criterion)
    {
        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * Remove criterion
     *
     * @param Criterion $criterion
     */
    public function removeCriterion(Criterion $criterion)
    {
        $this->criteria->removeElement($criterion);
    }

    /**
     * Get criteria
     *
     * @return Collection
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
     * @param Contributor $contributor
     *
     * @return Recommendation
     */
    public function setContributor(Contributor $contributor = null)
    {
        $this->contributor = $contributor;

        return $this;
    }

    /**
     * Get contributor
     *
     * @return Contributor
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


    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return ArrayCollection
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * @return int
     */
    public function getApprovedFeedbacksCount()
    {
        return $this->getFeedbacks()->filter(function(Feedback $feedback) {
            return $feedback->getType() == Feedback::APPROVE;
        })->count();
   }

    /**
     * @return int
     */
    public function getDismissedFeedbacksCount()
    {
        return $this->getFeedbacks()->filter(function(Feedback $feedback) {
            return $feedback->getType() == Feedback::DISMISS;
        })->count();
    }

    /**
     * @return int
     */
    public function getReportedFeedbacksCount()
    {
        return $this->getFeedbacks()->filter(function(Feedback $feedback) {
            return $feedback->getType() == Feedback::REPORT;
        })->count();
    }

    /**
     * Add channel
     *
     * @param \AppBundle\Entity\Channel $channel
     *
     * @return Recommendation
     */
    public function addChannel(\AppBundle\Entity\Channel $channel)
    {
        $this->channels[] = $channel;

        return $this;
    }

    /**
     * Remove channel
     *
     * @param \AppBundle\Entity\Channel $channel
     */
    public function removeChannel(\AppBundle\Entity\Channel $channel)
    {
        $this->channels->removeElement($channel);
    }

    /**
     * Get channels
     *
     * @return ArrayCollection
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Add feedback
     *
     * @param \AppBundle\Entity\Feedback $feedback
     *
     * @return Recommendation
     */
    public function addFeedback(\AppBundle\Entity\Feedback $feedback)
    {
        $this->feedbacks[] = $feedback;

        return $this;
    }

    /**
     * Remove feedback
     *
     * @param \AppBundle\Entity\Feedback $feedback
     */
    public function removeFeedback(\AppBundle\Entity\Feedback $feedback)
    {
        $this->feedbacks->removeElement($feedback);
    }
}
