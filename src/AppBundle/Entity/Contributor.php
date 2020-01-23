<?php

namespace AppBundle\Entity;

use AppBundle\Helper\ContributorSubscription;
use AppBundle\Helper\ImageUploadable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Contributor
 *
 * @ORM\Table(name="contributor")
 * @ORM\Entity
 * @Vich\Uploadable
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="string", length=255, nullable=true)
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
     * @ORM\OneToMany(targetEntity="Notice", mappedBy="contributor")
     * @ORM\OrderBy({"updated" = "ASC"})
     */
    private $notices;


    /**
     * @var Subscription[]
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="contributor")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    private $subscriptions;

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
     * @var \DateTime $updatedAt
     *
     * Needed to trigger forced update when an avatar is uploaded
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    private $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notices = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Contributor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setIntro(?string $intro) : Contributor
    {
        $this->intro = $intro;

        return $this;
    }

    public function getIntro() : ?string
    {
        return $this->intro;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add notice
     *
     * @param Notice $notice
     *
     * @return Contributor
     */
    public function addNotice(Notice $notice)
    {
        $this->notices[] = $notice;

        return $this;
    }

    /**
     * Remove notice
     *
     * @param Notice $notice
     */
    public function removeNotice(Notice $notice)
    {
        $this->notices->removeElement($notice);
    }

    public function getNotices() : ?Collection
    {
        return $this->notices;
    }

    public function getNoticesCount() : int
    {
        if ($notices = $this->getNotices()) {
            return $notices
                ->filter(function (Notice $notice) {
                    return $notice->hasPublicVisibility();
                })
                ->count();
        }
        else return 0;
    }

    public function getTheirMostLikedOrDisplayedNotice() : ?Notice
    {
        if ($notices = $this->getNotices()) {
            $noticesArray = $notices->toArray();
            return array_reduce($noticesArray, function (?Notice $acc, Notice $curr) {
                // First iteration...
                if (is_null($acc)) return $curr;

                // Compare likes at the first place...
                $currLikes = $curr->getLikedRatingCount();
                $accLikes = $acc->getLikedRatingCount();
                if ($currLikes > $accLikes) return $curr;
                if ($currLikes < $accLikes) return $acc;

                // Likes equality, compare displays then...
                $currDisplays = $curr->getDisplayedRatingCount();
                $accDisplays = $acc->getDisplayedRatingCount();
                if ($currDisplays > $accDisplays) return $curr;
                if ($currDisplays < $accDisplays) return $acc;

                // Likes and Displays equalities, just pick the first in...
                return $acc;
            });
        }
        else return null;
    }

    public function getTotalSubscriptions() : int
    {
        return $this->totalSubscriptions;
    }

    protected function addSubscription()
    {
        $this->totalSubscriptions = $this->totalSubscriptions + 1;
    }

    protected function removeSubscription()
    {
        if ($this->totalSubscriptions > 0) {
            $this->totalSubscriptions = $this->totalSubscriptions - 1;
        }
    }

    public function setTotalSubscriptionsFromRating(ContributorSubscription $rating) {
        if ($rating->is(ContributorSubscription::SUBSCRIBE)) {
            $this->addSubscription();
        }
        elseif ($rating->is(ContributorSubscription::UNSUBSCRIBE)) {
            $this->removeSubscription();
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

  /**
   * @return Subscription[]
   */
  public function getSubscriptions(): array
  {
    return $this->subscriptions;
  }
}
