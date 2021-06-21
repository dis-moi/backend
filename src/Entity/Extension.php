<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Extension.
 *
 * @ORM\Entity
 * @ORM\Table(name="extension")
 */
class Extension
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @var Collection<Subscription>
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="extension", orphanRemoval=true)
     * @ORM\OrderBy({"created"="DESC"})
     */
    private $subscriptions;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->created = new DateTime();
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Collection<Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function confirm(): void
    {
        $this->updated = new DateTime('now');
    }
}
