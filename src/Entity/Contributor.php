<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Model\Enum\CategoryName;
use App\Helper\ImageUploadable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Contributor.
 *
 * @ORM\Table(name="contributor")
 * @ORM\Entity
 * @Vich\Uploadable
 * @UniqueEntity("name")
 */
class Contributor implements ImageUploadable
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $intro;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="contributor_avatars", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @var string | null
     *
     * @ORM\Column(name="banner_image", type="string", length=255, nullable=true)
     */
    private $bannerImage;

    /**
     * @var File | null
     *
     * @Vich\UploadableField(mapping="contributor_banners", fileNameProperty="bannerImage")
     */
    private $bannerImageFile;

    /**
     * @var string
     *
     * @ORM\Column(name="preview_image", type="string", length=255, nullable=true)
     */
    private $previewImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="contributor_previews", fileNameProperty="previewImage")
     */
    private $previewImageFile;

    /**
     * @ORM\OneToMany(targetEntity="Notice", mappedBy="contributor")
     * @ORM\OrderBy({"updated" = "ASC"})
     */
    private $notices;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="contributor", orphanRemoval=true)
     * @ORM\OrderBy({"created" = "DESC"})
     */
    private $subscriptions;

    private $activeSubscriptionsCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="total_subscriptions", type="integer")
     */
    private $totalSubscriptions = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var DateTime
     *
     * Needed to trigger forced update when an avatar is uploaded
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=true)
     * @Assert\Length(max="255")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="website", type="string", length=255, unique=false, nullable=true)
     * @Assert\Length(max="255")
     */
    private $website;

    /** @var ArrayCollection|
     *
     * @ORM\OneToMany(targetEntity=Pin::class, mappedBy="contributor", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sort"="ASC"})
     */
    private $pins;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity=Relay::class, mappedBy="relayedBy", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $relayedNotices;

    /**
     * @var array
     *
     * @see CategoryName
     *
     * @ORM\Column(name="categories", type="simple_array", nullable=true)
     */
    private $categories = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->notices = new ArrayCollection();
        $this->relayedNotices = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    private function markUpdated(): Contributor
    {
        $this->updatedAt = new DateTime('now');

        return $this;
    }

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(?string $name): Contributor
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTitle(?string $title): Contributor
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setIntro(?string $intro): Contributor
    {
        $this->intro = $intro;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setImage(?string $image): Contributor
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImageFile(?File $image = null): Contributor
    {
        $this->imageFile = $image;
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            $this->markUpdated();
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Add notice.
     */
    public function addNotice(Notice $notice): Contributor
    {
        $this->notices[] = $notice;

        return $this;
    }

    /**
     * Remove notice.
     */
    public function removeNotice(Notice $notice): Contributor
    {
        $this->notices->removeElement($notice);

        return $this;
    }

    public function getNotices(): Collection
    {
        return $this->notices;
    }

    /*
     * @return int
     */
    public function getActiveSubscriptionsCount(): int
    {
        return $this->activeSubscriptionsCount;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled): Contributor
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * ⚠ Should not be used outside of repo.
     */
    public function setActiveSubscriptionsCount(int $activeSubscriptionsCount): Contributor
    {
        $this->activeSubscriptionsCount = $activeSubscriptionsCount;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Contributor
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Contributor
    {
        $this->website = $website;

        return $this;
    }

    public function getBannerImage(): ?string
    {
        return $this->bannerImage;
    }

    public function setBannerImage(?string $bannerImage): Contributor
    {
        $this->bannerImage = $bannerImage;

        return $this;
    }

    public function setBannerImageFile(?File $bannerImage = null): Contributor
    {
        $this->bannerImageFile = $bannerImage;
        if ($bannerImage) {
            $this->markUpdated();
        }

        return $this;
    }

    public function getBannerImageFile(): ?File
    {
        return $this->bannerImageFile;
    }

    public function setPreviewImage(?string $previewImage): Contributor
    {
        $this->previewImage = $previewImage;

        return $this;
    }

    public function getPreviewImage(): ?string
    {
        return $this->previewImage;
    }

    public function setPreviewImageFile(?File $previewImage = null): Contributor
    {
        $this->previewImageFile = $previewImage;

        if ($previewImage) {
            $this->markUpdated();
        }

        return $this;
    }

    public function getPreviewImageFile(): ?File
    {
        return $this->previewImageFile;
    }

    public function getPublicNotices(): Collection
    {
        return $this->getNotices()->filter(static function (Notice $notice) {
            return $notice->hasPublicVisibility();
        });
    }

    public function getPublicRelays(): Collection
    {
        return $this->getRelayedNotices()->filter(static function (Notice $notice) {
            return $notice->hasPublicVisibility();
        });
    }

    public function getPublicNoticesWithRelays(): ?array
    {
        return array_merge(
            $this->getPublicNotices()->toArray(),
            $this->getPublicRelays()->toArray()
        );
    }

    public function getNoticesCount(): int
    {
        if ($notices = $this->getPublicNotices()) {
            return $notices->count();
        }

        return 0;
    }

    public function getPinnedNotices(): ArrayCollection
    {
        return $this->pins
            ->matching(new Criteria(null, ['sort' => Criteria::ASC]))
            ->map(static function (Pin $pin) {
                return $pin->getNotice()->setPinnedSort($pin->getSort());
            });
    }

    public function setPinnedNotices(ArrayCollection $givenNotices): Contributor
    {
        if ($givenNotices->count() > 5) {
            throw new InvalidArgumentException('No more than 5 pinned notices by contributor please');
        }

        /** @var Notice $givenNotice */
        foreach ($givenNotices as $givenNotice) {
            $existingPin = $this->pins
                ->filter(function (Pin $existingPin) use ($givenNotice) {
                    return $existingPin->getNotice()->getId() === $givenNotice->getId();
                })
                ->first();

            if (null !== $givenNotice->getPinnedSort()) {
                if ($existingPin) {
                    $existingPin->setSort($givenNotice->getPinnedSort());
                } else {
                    $this->pins[] = new Pin($this, $givenNotice, $givenNotice->getPinnedSort());
                }
            }
        }

        foreach ($this->pins as $existingPin) {
            $inGiven = $givenNotices->filter(function (Notice $givenNotice) use ($existingPin) {
                return $givenNotice->getId() === $existingPin->getNotice()->getId();
            })->first();
            if (!$inGiven) {
                $this->pins->removeElement($existingPin);
            }
        }

        return $this;
    }

    public function getRelayedNotices(): Collection
    {
        return $this->relayedNotices->map(static function (Relay $relay) {
            return $relay->getNotice();
        });
    }

    public function addRelayedNotice(Notice $notice): Contributor
    {
        $this->relayedNotices[] = new Relay($this, $notice);

        return $this;
    }

    public function removeRelayedNotice(Notice $notice): Contributor
    {
        $this->relayedNotices->removeElement(current(array_filter($this->relayedNotices->toArray(), static function (Relay $relay) use ($notice) {
            return $relay->getNotice()->getId() === $notice->getId();
        })));

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function addCategory(string $categoryName): void
    {
        if (!in_array($categoryName, $this->categories)) {
            $this->categories[] = $categoryName;
        }
    }

    public function removeCategory(string $categoryName): void
    {
        $this->categories = array_filter(
            $this->categories,
            function ($cn) use ($categoryName) {
                return $cn !== $categoryName;
            }
        );
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
}
