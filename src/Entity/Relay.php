<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A contributor can relay a notice from another contributor. This entity represents this relation.
 *
 * @ORM\Entity
 * @ORM\Table(name="relay")
 * @UniqueEntity(
 *     fields={"contributor", "notice"},
 *     errorPath="contributor",
 *     message="This contributor already relayed that notice."
 * )
 */
class Relay
{
    /**
     * @var Contributor
     *
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="relayedNotices", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $relayedBy;

    /**
     * @var Notice
     *
     * @ORM\ManyToOne(targetEntity=Notice::class,inversedBy="relays", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $notice;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="relayed_at",type="datetime")
     */
    private $relayedAt;

    public function __construct(Contributor $contributor, Notice $notice)
    {
        $this->relayedAt = new DateTime();
        $this->notice = $notice;
        $this->relayedBy = $contributor;
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
