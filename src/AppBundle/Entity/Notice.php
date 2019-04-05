<?php

namespace AppBundle\Entity;

use AppBundle\Repository\MatchingContextRepository;
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
     * @ORM\ManyToOne(targetEntity=Intention::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="notice_type_id")
     */
    private $intention;

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

    public function getId() : int
    {
        return $this->id;
    }

    public function setMessage(string $message) : Notice
    {
        $this->message = strip_tags($message, '<p><a>');

        return $this;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function addMatchingContext(MatchingContext $matchingContext) : Notice
    {
        $matchingContext->setNotice($this);

        $this->matchingContexts[] = $matchingContext;

        return $this;
    }

    public function removeMatchingContext(MatchingContext $matchingContext)
    {
        $this->matchingContexts->removeElement($matchingContext);
    }

    public function getMatchingContexts() : ArrayCollection
    {
        return $this->matchingContexts;
    }

    public function setIntention(Intention $intention)
    {
        $this->intention = $intention;

        return $this;
    }

    public function getIntention() : Intention
    {
        return $this->intention;
    }

    public function __toString() : string
    {
        return sprintf('(id:%d) type: %s, contr: %s', $this->getId(), $this->getIntention(), $this->getContributor());
    }

    public function setContributor(Contributor $contributor = null) : Notice
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getContributor() : Contributor
    {
        return $this->contributor;
    }

    public function getVisibility() : NoticeVisibility
    {
        if (!$this->visibility) {
            return NoticeVisibility::getDefault();
        }
        return NoticeVisibility::get($this->visibility);
    }

    /**
     * @throw InvalidArgumentException
     */
    public function setVisibility(NoticeVisibility $visibility) : Notice
    {
        $this->visibility = $visibility->getValue();

        return $this;
    }

    public function hasPublicVisibility() : bool
    {
        return $this->getVisibility() === NoticeVisibility::PUBLIC_VISIBILITY();
    }

    public function getNote() : string
    {
        return $this->note;
    }

    public function setNote(string $note)
    {
        $this->note = $note;
    }

    public function getRatings() : ArrayCollection
    {
        return $this->ratings;
    }

    public function getDisplayedRatingCount(): int
    {
        return $this->getRatingCount(Rating::DISPLAY);
    }

    public function getUnfoldedRatingCount(): int
    {
        return $this->getRatingCount(Rating::UNFOLD);
    }

    public function getClickedRatingCount(): int
    {
        return $this->getRatingCount(Rating::OUTBOUND_CLICK_SOURCE)
            + $this->getRatingCount(Rating::OUTBOUND_CLICK_MESSAGE);
    }

    /**
     * @deprecated replaced by likes and dislikes
     */
    public function getApprovedRatingCount(): int
    {
        return 0;
    }

    public function getLikedRatingCount(): int
    {
        return $this->getRatingBalance(Rating::LIKE, Rating::UNLIKE);
    }

    public function getDislikedRatingCount(): int
    {
        return $this->getRatingBalance(Rating::DISLIKE, Rating::UNDISLIKE);
    }

    public function getDismissedRatingCount(): int
    {
        return $this->getRatingBalance(Rating::DISMISS, Rating::UNDISMISS);
    }

    public function getReportedRatingCount(): int
    {
        return $this->getRatingCount(Rating::REPORT);
    }

    protected function getRatingCount(string $type): int
    {
        return $this->getRatings()->filter(function(Rating $rating) use ($type) {
            return $rating->getType() === $type;
        })->count();
    }

    protected function getRatingBalance(string $typeUp , string $typeDown): int
    {
        $balance = $this->getRatingCount($typeUp) - $this->getRatingCount($typeDown);
        return $balance > 0 ? $balance : 0;
    }

    public function addChannel(Channel $channel) : Notice
    {
        $this->channels[] = $channel;

        return $this;
    }

    public function removeChannel(Channel $channel)
    {
        $this->channels->removeElement($channel);
    }

    public function getChannels() : ArrayCollection
    {
        return $this->channels;
    }

    public function addRating(Rating $rating) : Notice
    {
        $this->ratings[] = $rating;

        return $this;
    }

    public function removeRating(Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    public function getUpdated() : \DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function getSource() : Source
    {
        return $this->source;
    }

    public function setSource(Source $source)
    {
        $this->source = $source;
        $this->source->setNotice($this);
    }

    public function getSourceUrl() : ?string
    {
        return $this->source ? $this->source->getUrl() : null;
    }

    public function getAlternatives() : ArrayCollection
    {
        return $this->alternatives;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getExpires() : \DateTime
    {
        return $this->expires;
    }

    public function setExpires(\DateTime $expires)
    {
        $this->expires = $expires;
    }

    public function isUnpublishedOnExpiration() : bool
    {
        return $this->unpublishedOnExpiration;
    }

    public function setUnpublishedOnExpiration(bool $unpublishedOnExpiration)
    {
        $this->unpublishedOnExpiration = $unpublishedOnExpiration;
    }
}
