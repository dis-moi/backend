<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Ability\VichFilenamer;
use App\Helper\ImageUploadable;
use App\Serializer\V3\NormalizerOptions;
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
 * Contributors are the main actors of our domain, whereas User is reserved for authentication shenanigans.
 * Contributors emit Notices.
 * Every Contributor should at have at least one User that can impersonate it.
 *
 * @ORM\Table(name="contributor")
 * @ORM\Entity
 * @Vich\Uploadable
 * @UniqueEntity("name")
 * @ApiResource(
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"read"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *         },
 *     },
 *     collectionOperations={},
 * )
 */
class Contributor implements ImageUploadable
{
    use VichFilenamer;

    /**
     * A unique, incremental, numerical identifier for the Contributor.
     *
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
     * @var Collection<Notice>
     *
     * @ORM\OneToMany(targetEntity="Notice", mappedBy="contributor")
     * @ORM\OrderBy({"updated"="ASC"})
     */
    private $notices;

    /**
     * @var ArrayCollection<Subscription>
     *
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="contributor", orphanRemoval=true)
     * @ORM\OrderBy({"created"="DESC"})
     */
    private $subscriptions;

    /**
     * @var int
     */
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
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=true)
     * @Assert\Length(max="255")
     * @Assert\Email
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="website", type="string", length=255, unique=false, nullable=true)
     * @Assert\Length(max="255")
     */
    private $website;

    /**
     * @var ArrayCollection<Pin>
     * @ORM\OneToMany(targetEntity=Pin::class, mappedBy="contributor", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sort"="ASC"})
     */
    private $pins;

    /**
     * @var ArrayCollection<Relay>
     *
     * @ORM\OneToMany(targetEntity=Relay::class, mappedBy="relayedBy", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $relayedNotices;

    /**
     * @var string[]
     *
     * @ORM\Column(name="categories", type="simple_array", nullable=true)
     */
    private $categories = [];

    /**
     * The list of Users that may impersonate this Contributor.
     * Some users with the appropriate roles may also impersonate without being referenced explicitly here. (admins).
     *
     * @var ArrayCollection<User>
     * @ORM\ManyToMany(
     *     targetEntity=User::class,
     *     mappedBy="hats",
     *     cascade={"persist"},
     * )
     */
    private $impersonators;

    public function __construct()
    {
        $this->notices = new ArrayCollection();
        $this->pins = new ArrayCollection();
        $this->relayedNotices = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->impersonators = new ArrayCollection();
    }

    private function markUpdated(): self
    {
        $this->updatedAt = new DateTime('now');

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImageFile(?File $image = null): self
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
    public function addNotice(Notice $notice): self
    {
        $this->notices[] = $notice;

        return $this;
    }

    /**
     * Remove notice.
     */
    public function removeNotice(Notice $notice): self
    {
        $this->notices->removeElement($notice);

        return $this;
    }

    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function getActiveSubscriptionsCount(): int
    {
        return $this->activeSubscriptionsCount;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * ⚠ Should not be used outside of repo.
     */
    public function setActiveSubscriptionsCount(int $activeSubscriptionsCount): self
    {
        $this->activeSubscriptionsCount = $activeSubscriptionsCount;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getBannerImage(): ?string
    {
        return $this->bannerImage;
    }

    public function setBannerImage(?string $bannerImage): self
    {
        $this->bannerImage = $bannerImage;

        return $this;
    }

    public function setBannerImageFile(?File $bannerImage = null): self
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

    public function setPreviewImage(?string $previewImage): self
    {
        $this->previewImage = $previewImage;

        return $this;
    }

    public function getPreviewImage(): ?string
    {
        return $this->previewImage;
    }

    public function setPreviewImageFile(?File $previewImage = null): self
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

    public function getPublicRelays(): ArrayCollection
    {
        return $this->getRelayedNotices()->filter(static function (Notice $notice) {
            return $notice->hasPublicVisibility();
        });
    }

    /**
     * @return Notice[]
     */
    public function getPublicNoticesWithRelays(): array
    {
        return array_merge(
            $this->getPublicNotices()->toArray(),
            $this->getPublicRelays()->toArray()
        );
    }

    public function getNoticesCount(): int
    {
        return $this->getPublicNotices()->count();
    }

    /**
     * @return Collection<Notice>
     */
    public function getPinnedNotices(): Collection
    {
        return $this->pins
            ->matching(new Criteria(null, ['sort' => Criteria::ASC]))
            ->map(static function (Pin $pin) {
                return $pin->getNotice()->setPinnedSort($pin->getSort());
            });
    }

    /**
     * @param ArrayCollection<Notice> $givenNotices
     *
     * @return $this
     */
    public function setPinnedNotices(ArrayCollection $givenNotices): self
    {
        if ($givenNotices->count() > 5) {
            throw new InvalidArgumentException('No more than 5 pinned notices by contributor please');
        }

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
            if ( ! $inGiven) {
                $this->pins->removeElement($existingPin);
            }
        }

        return $this;
    }

    public function getRelayedNotices(): ArrayCollection
    {
        return $this->relayedNotices->map(static function (Relay $relay) {
            return $relay->getNotice();
        });
    }

    public function addRelayedNotice(Notice $notice): self
    {
        $this->relayedNotices[] = new Relay($this, $notice);

        return $this;
    }

    public function removeRelayedNotice(Notice $notice): self
    {
        $this->relayedNotices->removeElement(current(array_filter($this->relayedNotices->toArray(), static function (Relay $relay) use ($notice) {
            return $relay->getNotice()->getId() === $notice->getId();
        })));

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function addCategory(string $categoryName): void
    {
        if ( ! \in_array($categoryName, $this->categories, true)) {
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

    /**
     * @param string[] $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return ArrayCollection<User> Those that may act as this Contributor.
     */
    public function getImpersonators(): ArrayCollection
    {
        return $this->impersonators;
    }

    public function hasImpersonator(User $user): bool
    {
        return $this->impersonators->contains($user);
    }

    public function addImpersonator(User $user): self
    {
        if ( ! $this->hasImpersonator($user)) {
            $this->impersonators->add($user);
            $user->addHat($this);  // update inverse
        }

        return $this;
    }

    public function removeImpersonator(User $user): self
    {
        if ($this->impersonators->contains($user)) {
            $this->impersonators->removeElement($user);
            $user->removeHat($this);  // update inverse
        }

        return $this;
    }
}
