<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A contributor can pin up to 5 notices to show at the top of its profile. This entity represents this relation.
 *
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="pins", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $contributor;

    /**
     * @var Notice
     *
     * @ORM\ManyToOne(targetEntity=Notice::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $notice;

    /**
     * @var int
     *
     * @ORM\Column(name="sort",type="integer")
     */
    private $sort;

    public function __construct(Contributor $contributor, Notice $notice, int $sort)
    {
        $this->notice = $notice;
        $this->contributor = $contributor;
        $this->sort = $sort;
    }

    public function __toString(): string
    {
        return (string) $this->notice;
    }

    public function getContributor(): Contributor
    {
        return $this->contributor;
    }

    public function setContributor(Contributor $contributor): Pin
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getNotice(): Notice
    {
        return $this->notice;
    }

    public function setNotice(Notice $notice): Pin
    {
        $this->notice = $notice;

        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): Pin
    {
        $this->sort = $sort;

        return $this;
    }
}
