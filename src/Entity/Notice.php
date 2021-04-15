<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\EntityListener\NoticeListener;
use App\Helper\NoticeVisibility;
use App\Helper\StringHelper;
use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * A Notice holds a message written/contributed by a Contributor about a web page
 * or a set of web pages selected by the matching contexts.
 * This is the main content of the application.
 *
 * @ORM\Table(name="notice")
 * @ORM\Entity(repositoryClass="App\Repository\NoticeRepository")
 * @ORM\EntityListeners({NoticeListener::class})
 * @Vich\Uploadable
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={"groups"={"read"}},
 *         },
 *         "delete"={
 *             "access_control"="is_granted('can_delete', object)",
 *         },
 *     },
 *     collectionOperations={
 *         "get"={
 *             "normalization_context"={"groups"={"read"}},
 *         },
 *         "post"={
 *             "denormalization_context"={"groups"={"create"}},
 *         },
 *     },
 * )
 */
class Notice
{
    // Goutte: Should we add <b><em><sup><sub> here as well?
    public const ALLOWED_TAGS = '<p><a>';

    /**
     * A unique, incremental, numerical identifier for the Notice.
     *
     * @var int
     * @Groups({"read"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ApiProperty(
     *     openapiContext={
     *         "example": 42,
     *     },
     * )
     */
    private $id;

    /**
     * The visibility of the Notice.  See `NoticeVisibility`
     * for an enumeration of the allowed values:
     *   - "public": anyone may view this Notice
     *   - "private": Notice is only visible to Contributor
     *   - "archived": ???
     *   - "draft": Notice is only visible to Contributor, pending publication
     *   - "question": ???
     *
     * @var ?string
     * @Groups({"read", "create"})
     * @ORM\Column(
     *     name="visibility",
     *     type="string",
     *     options={
     *         "default"=NoticeVisibility::PRIVATE_VISIBILITY,
     *     },
     * )
     */
    private $visibility;

    /**
     * @var ArrayCollection<MatchingContext>
     *
     * @ORM\OneToMany(targetEntity=MatchingContext::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid
     */
    private $matchingContexts;

    // Goutte: Is this still used?  Isn't it in matchingContexts?  Can anyone document?
    /**
     * @var string
     *
     * @ORM\Column(name="excludeUrlRegex", type="text", nullable=true)
     */
    private $excludeUrlRegex;

    /**
     * The Contributor who submitted the Notice.
     * @var Contributor
     *
     * @Groups({"read"})
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="notices", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contributor;

    /**
     * The message attached to the Notice, ie. what the user wants to read,
     * the main content of DisMoi, the added value, etc.  It is HTML,
     * and is "purified", ie. is stripped of HTML tags not in ALLOWED_TAGS.
     * @var string
     *
     * @Groups({"read", "create"})
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
     * @var ArrayCollection<Rating>
     *
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ratings;

    /**
     * @var DateTime
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var DateTime
     *
     * @Groups({"read"})
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
     * @ORM\OneToMany(targetEntity=Relay::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $relays;

    /**
     * This is only used a temporary storage when saving pins in form (Ugly).
     *
     * @var int
     */
    private $pinnedSort;

    /**
     * @var string
     *
     * @ORM\Column(name="externalId", type="string", length=25, nullable=true)
     */
    private $externalId;

    public function __construct()
    {
        $this->matchingContexts = new ArrayCollection();
        $this->relays = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->visibility = NoticeVisibility::getDefault()->getValue();
        $this->expires = (new DateTimeImmutable())->modify('+1year');
    }

    public static function equals(self $notice): Closure
    {
        return static function (self $other) use ($notice) {
            return $notice->getId() === $other->getId();
        };
    }

    private function markUpdated(): void
    {
        $this->updated = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setMessage(string $message): self
    {
        $this->message = strip_tags((new \HTMLPurifier())->purify($message), self::ALLOWED_TAGS);

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function addMatchingContext(MatchingContext $matchingContext): self
    {
        $matchingContext->setNotice($this);

        $this->matchingContexts[] = $matchingContext;

        return $this;
    }

    public function removeMatchingContext(MatchingContext $matchingContext): void
    {
        $this->matchingContexts->removeElement($matchingContext);
    }

    public function getMatchingContexts(): ?Collection
    {
        return $this->matchingContexts;
    }

    public function __toString(): string
    {
        return sprintf('(id:%d) [%s] %s', $this->getId(), $this->getContributor(), StringHelper::truncate($this->getMessage(), 45));
    }

    public function setContributor(Contributor $contributor = null): self
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
     * @param NoticeVisibility|string $visibility
     * @return $this
     */
    public function setVisibility($visibility): self
    {
        if ($visibility instanceof NoticeVisibility) {
            $this->visibility = $visibility->getValue();
        } else {
            $this->visibility = $visibility;
        }

        return $this;
    }

    public function hasPublicVisibility(): bool
    {
        return $this->getVisibility() === NoticeVisibility::PUBLIC_VISIBILITY;
    }

    public function isUnpublished(): bool
    {
        return null !== $this->getExpires() && $this->isUnpublishedOnExpiration() && $this->getExpires() < new DateTime('now');
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
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

    public function addRating(Rating $rating): self
    {
        $this->ratings[] = $rating;

        return $this;
    }

    public function removeRating(Rating $rating): void
    {
        $this->ratings->removeElement($rating);
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
        $this->setUpdated($created);
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated): void
    {
        $this->updated = $updated;
    }

    public function getExpires(): ?DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(DateTime $expires = null): void
    {
        $this->expires = $expires;
    }

    public function isUnpublishedOnExpiration(): bool
    {
        return $this->unpublishedOnExpiration;
    }

    public function setUnpublishedOnExpiration(bool $unpublishedOnExpiration): void
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

    public function setExcludeUrlRegex(?string $excludeUrlRegex): void
    {
        $this->excludeUrlRegex = $excludeUrlRegex;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(?string $screenshot): self
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    public function getScreenshotFile(): ?File
    {
        return $this->screenshotFile;
    }

    public function setScreenshotFile(?File $screenshotFile): self
    {
        $this->screenshotFile = $screenshotFile;
        if ($screenshotFile) {
            $this->markUpdated();
        }

        return $this;
    }

    /**
     * @deprecated use `getExampleMatchingUrl` instead
     */
    public function getExampleUrl(): ?string
    {
        return $this->getExampleMatchingUrl();
    }

    public function getExampleMatchingUrl(): ?string
    {
        $first = $this->getMatchingContexts()
            ->filter(function (MatchingContext $mc) {
                return (bool) $mc->getExampleUrl();
            })
            ->first();
        if ($first) {
            return $first->getExampleUrl();
        }

        return null;
    }

    public function getRelayers(): Collection
    {
        return $this->relays->map(static function (Relay $relay) {
            return $relay->getRelayedBy();
        });
    }

    public function addRelayer(Contributor $contributor): self
    {
        $this->relays[] = new Relay($contributor, $this);

        return $this;
    }

    public function removeRelayer(Contributor $contributor): self
    {
        $this->relays->removeElement(
            current(
                array_filter(
                    $this->relays->toArray(),
                    static function (Relay $relay) use ($contributor) {
                        return $relay->getRelayedBy()->getId() === $contributor->getId();
                    }
                )
            )
        );

        return $this;
    }

    public function getRelayersCount(): int
    {
        return $this->getRelayers()->count();
    }

    public function getPinnedSort(): ?int
    {
        return $this->pinnedSort;
    }

    public function setPinnedSort(int $pinnedSort): self
    {
        $this->pinnedSort = $pinnedSort;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }
}
