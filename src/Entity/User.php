<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use App\Entity\Contributor;

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
     * @var ArrayCollection<Contributor>
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
     * @return ArrayCollection<Contributor>
     */
    public function getHats()
    {
        return $this->hats;
    }

    public function addHat(Contributor $contributor)
    {
        $this->hats->add($contributor);
    }

    public function removeHat(Contributor $contributor)
    {
        if ($this->hats->contains($contributor)) {
            $this->hats->removeElement($contributor);
        }
    }
}
