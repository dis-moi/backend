<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Everyone's a User.
 * Holds the auth nitty-gritty (see parent FOS).
 * A User may control multiple Contributors, ie. wear many hats.
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Contributors this User may impersonate, that is can submit Notices as.
     *
     * @var Collection<Contributor>
     * @ORM\ManyToMany(
     *     targetEntity=Contributor::class,
     *     inversedBy="impersonators",
     *     cascade={"persist"},
     * )
     */
    private $hats;

    public function __construct()
    {
        parent::__construct();
        $this->hats = new ArrayCollection();
    }

    /**
     * @return Collection<Contributor>
     */
    public function getHats()
    {
        return $this->hats;
    }

    public function addHat(Contributor $contributor): self
    {
        if ( ! $this->hats->contains($contributor)) {
            $this->hats->add($contributor);
        }

        return $this;
    }

    public function removeHat(Contributor $contributor): self
    {
        if ($this->hats->contains($contributor)) {
            $this->hats->removeElement($contributor);
        }

        return $this;
    }
}
