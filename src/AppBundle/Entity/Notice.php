<?php

namespace AppBundle\Entity;

use AppBundle\EntityListener\NoticeListener;
use AppBundle\Helper\NoticeVisibility;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Notice.
 *
 * @ORM\Table(name="notice")
 * @ORM\Entity
 * @ORM\EntityListeners({NoticeListener::class})
 * @Vich\Uploadable
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
     * @var string
     *
     * @ORM\Column(name="excludeUrlRegex", type="text", nullable=true)
     */
    private $excludeUrlRegex;

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
     * @var string?
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
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $unpublishedOnExpiration = false;

    /**
     * @var string
     *
     * @ORM\Column(name="screenshot", type="string", length=255, nullable=true)
     */
    private $screenshot;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="notice_screenshots", fileNameProperty="screenshot")
     */
    private $screenshotFile;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity=Relay::class, mappedBy="notice", cascade={"persist"})
     */
    private $relays;

    public function __construct()
    {
        $this->matchingContexts = new ArrayCollection();
        $this->relays = new ArrayCollection();
        $this->visibility = NoticeVisibility::getDefault();
        $this->expires = (new DateTimeImmutable())->modify('+1year');
    }

    private function markUpdated(): void
    {
        $this->updated = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setMessage(string $message): Notice
    {
        $this->message = strip_tags($message, '<p><a>');

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function addMatchingContext(MatchingContext $matchingContext): Notice
    {
        $matchingContext->setNotice($this);

        $this->matchingContexts[] = $matchingContext;

        return $this;
    }

    public function removeMatchingContext(MatchingContext $matchingContext)
    {
        $this->matchingContexts->removeElement($matchingContext);
    }

    public function getMatchingContexts(): ?Collection
    {
        return $this->matchingContexts;
    }

    public function __toString(): string
    {
        return sprintf('(id:%d) [%s] %s', $this->getId(), $this->getContributor(), $this->getMessage());
    }

    public function setContributor(Contributor $contributor = null): Notice
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getContributor(): ?Contributor
    {
        return $this->contributor;
    }

    public function getVisibility(): ?NoticeVisibility
    {
        if (!$this->visibility) {
            return NoticeVisibility::getDefault();
        }

        return NoticeVisibility::get($this->visibility);
    }

    /**
     * @throw InvalidArgumentException
     */
    public function setVisibility(NoticeVisibility $visibility): Notice
    {
        $this->visibility = $visibility->getValue();

        return $this;
    }

    public function hasPublicVisibility(): bool
    {
        return $this->getVisibility() === NoticeVisibility::PUBLIC_VISIBILITY();
    }

    public function isUnpublished(): bool
    {
        return null !== $this->getExpires() && $this->isUnpublishedOnExpiration() && $this->getExpires() < new DateTime('now');
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note)
    {
        $this->note = $note;
    }

    public function getRatings(): ?Collection
    {
        return $this->ratings;
    }

    public function getBadgedRatingCount(): int
    {
        return $this->getRatingCount(Rating::BADGE);
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
        return $this->getRatingCount(Rating::OUTBOUND_CLICK);
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
        return $this->getRatings()->filter(function (Rating $rating) use ($type) {
            return $rating->getType() === $type;
        })->count();
    }

    protected function getRatingBalance(string $typeUp, string $typeDown): int
    {
        $balance = $this->getRatingCount($typeUp) - $this->getRatingCount($typeDown);

        return $balance > 0 ? $balance : 0;
    }

    public function addRating(Rating $rating): Notice
    {
        $this->ratings[] = $rating;

        return $this;
    }

    public function removeRating(Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
        $this->setUpdated($created);
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function getExpires(): ?\DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(\DateTime $expires = null)
    {
        $this->expires = $expires;
    }

    public function isUnpublishedOnExpiration(): bool
    {
        return $this->unpublishedOnExpiration;
    }

    public function setUnpublishedOnExpiration(bool $unpublishedOnExpiration)
    {
        $this->unpublishedOnExpiration = $unpublishedOnExpiration;
    }

    /**
     * @return string?
     */
    public function getExcludeUrlRegex(): ?string
    {
        return $this->excludeUrlRegex;
    }

    /**
     * @param string? $excludeUrlRegex
     */
    public function setExcludeUrlRegex(?string $excludeUrlRegex): void
    {
        $this->excludeUrlRegex = $excludeUrlRegex;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(?string $screenshot): Notice
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    public function getScreenshotFile(): ?File
    {
        return $this->screenshotFile;
    }

    public function setScreenshotFile(?File $screenshotFile): Notice
    {
        $this->screenshotFile = $screenshotFile;
        if ($screenshotFile) {
            $this->markUpdated();
        }

        return $this;
    }

    public function getExampleUrl(): ?string
    {
        $first = $this->getMatchingContexts()
            ->filter(function (MatchingContext $mc) {
                return (bool) $mc->getExampleUrl();
            })
            ->first();
        if ($first) {
            return $first->getExampleUrl();
        } else {
            return null;
        }
    }

    public function getRelayers(): Collection
    {
        return $this->relays->map(static function (Relay $relay) {
            return $relay->getRelayedBy();
        });
    }

    public function addRelayer(Contributor $contributor): Notice
    {
        $this->relays[] = new Relay($contributor, $this);

        return $this;
    }

    public function removeRelayer(Contributor $contributor): Notice
    {
        $this->relays->removeElement(current(array_filter($this->relays->toArray(), static function (Relay $relay) use ($contributor) {
            return $relay->getRelayedBy()->getId() === $contributor->getId();
        })));

        return $this;
    }

    public function getRelayersCount(): int
    {
        return $this->getRelayers()->count();
    }
}
