<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Helper\NoticeVisibility;
use AppBundle\EntityListener\NoticeListener;

/**
 * Notice
 *
 * @ORM\Table(name="notice")
 * @ORM\Entity
 * @ORM\EntityListeners({NoticeListener::class})
 */
class Notice
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
     * @ORM\OneToMany(targetEntity="Alternative", mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @deprecated since API v3, will be removed soon
     */
    private $alternatives;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @deprecated since API v3, will be removed soon
     */
    private $title;

    /**
     * @ORM\Column(name="visibility", type="string", options={"default" : "private"})
     */
    private $visibility;

    /**
     * @ORM\OneToMany(targetEntity=MatchingContext::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid
     */
    private $matchingContexts;

    /**
     * @ORM\ManyToMany(targetEntity=Channel::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, name="recommendation_channel")
     */
    private $channels;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="notice_type_id")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="notices", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contributor;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ratings;

    /**
     * @var Source
     *
     * @ORM\OneToOne(targetEntity=Source::class, mappedBy="notice", cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
     */
    private $source;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var \DateTime $expires
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires = null;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $unpublishedOnExpiration = false;

    public function __construct()
    {
        $this->alternatives = new ArrayCollection();

        $this->channels = new ArrayCollection();
        $this->matchingContexts = new ArrayCollection();
        $this->visibility = NoticeVisibility::getDefault();
    }

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
     * Set message
     *
     * @param string $message
     *
     * @return Notice
     */
    public function setMessage($message)
    {
        $this->message = strip_tags($message, '<p><a>');

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add matchingContext
     *
     * @param MatchingContext $matchingContext
     *
     * @return Notice
     */
    public function addMatchingContext(MatchingContext $matchingContext)
    {
        $matchingContext->setNotice($this);

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

    public function setType(Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('(id:%d) type: %s, contr: %s', $this->getId(), $this->getType(), $this->getContributor());
    }

    /**
     * Set contributor
     *
     * @param Contributor $contributor
     *
     * @return Notice
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
     * @return NoticeVisibility
     */
    public function getVisibility()
    {
        if (!$this->visibility) {
            return NoticeVisibility::getDefault();
        }
        return NoticeVisibility::get($this->visibility);
    }

    /**
     * @param NoticeVisibility $visibility
     * @throw InvalidArgumentException
     * @return Notice
     */
    public function setVisibility(NoticeVisibility $visibility)
    {
        $this->visibility = $visibility->getValue();

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPublicVisibility()
    {
        return $this->getVisibility() === NoticeVisibility::PUBLIC_VISIBILITY();
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
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @return int
     */
    public function getDisplayedRatingCount()
    {
        return $this->getRatingCount(Rating::DISPLAY);
    }

    /**
     * @return int
     */
    public function getClickedRatingCount()
    {
        return $this->getRatingCount(Rating::CLICK);
    }

    /**
     * @return int
     */
    public function getApprovedRatingCount()
    {
        return $this->getRatingCount(Rating::APPROVE);
   }

    /**
     * @return int
     */
    public function getDismissedRatingCount()
    {
        return $this->getRatingCount(Rating::DISMISS);
    }

    /**
     * @return int
     */
    public function getReportedRatingCount()
    {
        return $this->getRatingCount(Rating::REPORT);
    }

    /**
     * @param string $type
     * @return int
     */
    protected function getRatingCount($type)
    {
        return $this->getRatings()->filter(function(Rating $rating) use ($type) {
            return $rating->getType() === $type;
        })->count();
    }

    /**
     * Add channel
     *
     * @param Channel $channel
     *
     * @return Notice
     */
    public function addChannel(Channel $channel)
    {
        $this->channels[] = $channel;

        return $this;
    }

    /**
     * Remove channel
     *
     * @param Channel $channel
     */
    public function removeChannel(Channel $channel)
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
     * Add rating
     *
     * @param Rating $rating
     *
     * @return Notice
     */
    public function addRating(Rating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating
     *
     * @param Rating $rating
     */
    public function removeRating(Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param Source $source
     */
    public function setSource(Source $source)
    {
        $this->source = $source;
        $this->source->setNotice($this);
    }

    public function getSourceUrl()
    {
        return $this->source->getUrl();
    }


    public function getAlternatives()
    {
        return $this->alternatives;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param \DateTime $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return bool
     */
    public function isUnpublishedOnExpiration()
    {
        return $this->unpublishedOnExpiration;
    }

    /**
     * @param bool $unpublishedOnExpiration
     */
    public function setUnpublishedOnExpiration($unpublishedOnExpiration)
    {
        $this->unpublishedOnExpiration = $unpublishedOnExpiration;
    }
}
