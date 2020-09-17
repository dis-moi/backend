<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A contributor can pin up to 5 notices to show at the top of its profile. This entity represents this relation.
 *
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="pin")
 * @UniqueEntity(
 *     fields={"contributor", "notice"},
 *     errorPath="contributor",
 *     message="This contributor already pinned that notice."
 * )
 */
class Pin
{
    /**
     * @var Contributor
     *
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="pins", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $contributor;

    /**
     * @var Notice
     *
     * @ORM\ManyToOne(targetEntity=Notice::class,inversedBy="pins", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $notice;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="rank",type="integer")
     */
    private $rank;

    public function __construct(Contributor $contributor, Notice $notice)
    {
        $this->notice = $notice;
        $this->contributor = $contributor;
    }

    public function __toString()
    {
        return "{$this->relayedBy} relayed {$this->notice}";
    }

    public function getRelayedBy(): Contributor
    {
        return $this->relayedBy;
    }

    public function setRelayedBy(Contributor $relayedBy): Relay
    {
        $this->relayedBy = $relayedBy;

        return $this;
    }

    public function getNotice(): Notice
    {
        return $this->notice;
    }

    public function setNotice(Notice $notice): Relay
    {
        $this->notice = $notice;

        return $this;
    }

    public function getRelayedAt(): DateTime
    {
        return $this->relayedAt;
    }

    public function setRelayedAt(DateTime $relayedAt): Relay
    {
        $this->relayedAt = $relayedAt;

        return $this;
    }
}
