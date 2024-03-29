<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\EntityListener\NoticeListener;
use App\Helper\NoticeVisibility;
use App\Helper\StringHelper;
use App\Serializer\V3\NormalizerOptions;
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
 *     normalizationContext={
 *         "groups"={"read"},
 *         NormalizerOptions::VERSION=4,
 *     },
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"read"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *         },
 *         "delete"={
 *             "access_control"="is_granted('can_delete', object)",
 *         },
 *     },
 *     collectionOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"read"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *         },
 *         "post"={
 *             "denormalization_context"={
 *                 "groups"={"create"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *             "access_control"="is_granted('can_create', object)",
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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read"})
     * @ApiProperty(
     *     openapiContext={
     *         "example"=42,
     *     },
     * )
     */
    private $id;

    /**
     * The visibility of the Notice.  See `NoticeVisibility`
     * for an enumeration of the allowed values:
     *   - "public": anyone may view this Notice
     *   - "private": Notice is only visible to Contributor
     *   - "archived": A deleted notice because it wasn't relevant anymore
     *   - "draft": Notice is only visible to Contributor, pending publication
     *   - "question": A question asked by a someone on a webpage.  A question is not publicly visible.
     *
     * @var ?string
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
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
     * @var Collection<MatchingContext>
     *
     * @ORM\OneToMany(targetEntity=MatchingContext::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     *
     * @ApiSubresource
     * @Assert\Valid
     */
    private $matchingContexts;

    /**
     * A global exclude rule that applies to all matching contexts.
     *
     * @var string
     *
     * @ORM\Column(name="excludeUrlRegex", type="text", nullable=true)
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $excludeUrlRegex;

    /**
     * The Contributor who submitted the Notice.
     *
     * @var Contributor
     *
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="notices", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contributor;

    /**
     * The raw message attached to the Notice,
     * as given by the Contributor.  It is unsafe to read from it,
     * prefer reading from `strippedMessage`.
     *
     * @var string
     *
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
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
     * @var Collection<Rating>
     *
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="notice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ratings;

    /**
     * @var int
     *
     * @ORM\Column(name="badged_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $badgedCount = 0;

    /**
     * The number of time the notice has been displayed in a list.
     *
     * @var int
     *
     * @ORM\Column(name="displayed_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $displayedCount = 0;

    /**
     * The number of time the notice has been displayed in full.
     *
     * @var int
     *
     * @ORM\Column(name="unfolded_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $unfoldedCount = 0;

    /**
     * The number of time the notice has been clicked.
     *
     * @var int
     *
     * @ORM\Column(name="clicked_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $clickedCount = 0;

    /**
     * The number of time the notice has been liked.
     *
     * @var int
     *
     * @ORM\Column(name="liked_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $likedCount = 0;

    /**
     * The number of time the notice has been disliked.
     *
     * @var int
     *
     * @ORM\Column(name="disliked_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $dislikedCount = 0;

    /**
     * The number of time the notice has been dismissed.
     *
     * @var int
     *
     * @ORM\Column(name="dismissed_count", type="integer", nullable=false, options={"default"=0})
     * @Groups({"read"})
     */
    private $dismissedCount = 0;

    /**
     * Latest update date of the notice, serialized in the ISO8601 format.
     *
     * @var DateTime
     *
     * Groups({"read"}) → see getModified()
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * Creation date of the notice, serialized in the ISO8601 format.
     *
     * @var DateTime
     *
     * @Groups({"read"})
     * @ApiProperty(
     *     readable=true,
     *     writable=false,
     *     openapiContext={
     *         "example"="2021-04-16T14:59:37+02:00",
     *     },
     * )
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Expiration date of the notice, in the ISO8601 format.
     *
     * @var DateTimeInterface
     * @Groups({
     *     "read",
     *     "create",
     *     "update",
     * })
     * @ApiProperty(
     *     openapiContext={
     *         "example"="2031-11-05T00:00:00+02:00",
     *     },
     * )
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Groups({
     *     "read",
     *     "create",
     *     "update",
     * })
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

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $locale;

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

    public function __toString(): string
    {
        return sprintf('(id:%d) [%s] %s', $this->getId(), $this->getContributor(), StringHelper::truncate($this->getMessage(), 45));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Last modification date of the notice, serialized in the ISO8601 format.
     *
     * This "property" exists here for ApiPlatform documentation.
     *
     * @Groups({"read"})
     * @ApiProperty(
     *     readable=true,
     *     writable=false,
     *     example="2021-03-21T14:42:00+02:00"
     * )
     */
    public function getModified(): DateTime
    {
        return $this->getUpdated();
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * The message attached to the Notice, ie. what the user wants to read,
     * the main content of DisMoi, the added value, etc.  It is HTML,
     * and is "purified", ie. is stripped of HTML tags not in ALLOWED_TAGS.
     *
     * This "property" exists here for ApiPlatform documentation.
     *
     * @Groups({"read"})
     * @ApiProperty(
     *     readable=true,
     *     writable=false,
     * )
     */
    public function getStrippedMessage(): string
    {
        return $this->getMessage();
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = strip_tags((new \HTMLPurifier())->purify($message), self::ALLOWED_TAGS);

        return $this;
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

    public function getContributor(): ?Contributor
    {
        return $this->contributor;
    }

    public function setContributor(Contributor $contributor = null): self
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getVisibility(): ?NoticeVisibility
    {
        if ( ! $this->visibility) {
            return NoticeVisibility::getDefault();
        }

        return NoticeVisibility::get($this->visibility);
    }

    /**
     * @param NoticeVisibility|string $visibility
     *
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
        return $this->getVisibility() === NoticeVisibility::PUBLIC_VISIBILITY();
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

    /**
     * Amount of likes the Notice has received.
     *
     * @Groups({"read"})
     * @ApiProperty(
     *     readable=true,
     *     writable=false,
     * )
     */
    public function getLikes(): int
    {
        return $this->getLikedCount();
    }

    /**
     * Amount of dislikes the Notice has received.
     *
     * @Groups({"read"})
     * @ApiProperty(
     *     readable=true,
     *     writable=false,
     * )
     */
    public function getDislikes(): int
    {
        return $this->getDislikedCount();
    }

    public function getBadgedCount(): int
    {
        return $this->badgedCount;
    }

    public function setBadgedCount(int $count): self
    {
        $this->badgedCount = $count;

        return $this;
    }

    public function getDisplayedCount(): int
    {
        return $this->displayedCount;
    }

    public function setDisplayedCount(int $count): self
    {
        $this->displayedCount = $count;

        return $this;
    }

    public function getUnfoldedCount(): int
    {
        return $this->unfoldedCount;
    }

    public function setUnfoldedCount(int $count): self
    {
        $this->unfoldedCount = $count;

        return $this;
    }

    public function getClickedCount(): int
    {
        return $this->clickedCount;
    }

    public function setClickedCount(int $count): self
    {
        $this->clickedCount = $count;

        return $this;
    }

    public function getLikedCount(): int
    {
        return $this->likedCount;
    }

    public function setLikedCount(int $count): self
    {
        $this->likedCount = $count;

        return $this;
    }

    public function getDislikedCount(): int
    {
        return $this->dislikedCount;
    }

    public function setDislikedCount(int $count): self
    {
        $this->dislikedCount = $count;

        return $this;
    }

    public function getDismissedCount(): int
    {
        return $this->dismissedCount;
    }

    public function setDismissedCount(int $count): self
    {
        $this->dismissedCount = $count;

        return $this;
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

    private function markUpdated(): void
    {
        $this->updated = new DateTime('now');
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

    public function getMatchingContexts(): ?Collection
    {
        return $this->matchingContexts;
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

    public function getRelayers(): Collection
    {
        return $this->relays->map(static function (Relay $relay) {
            return $relay->getRelayedBy();
        });
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

    public function getLocale(): string
    {
        if (isset($this->locale)) {
            return $this->locale;
        }
        if ($this->getContributor()) {
            return $this->getContributor()->getLocale();
        }

        return \Locale::getDefault();
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
