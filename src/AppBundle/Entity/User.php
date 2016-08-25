<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
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
     * @ORM\OneToOne(targetEntity="Contributor", mappedBy="user", fetch="EAGER")
     */
    private $contributor;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return \AppBundle\Entity\Contributor
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * @param \AppBundle\Entity\Contributor $contributor
     * @return User
     */
    public function setContributor(Contributor $contributor)
    {
        $contributor->setUser($this);
        $this->contributor = $contributor;
        return $this;
    }

    /**
     * @param boolean $isSuperAdmin
     * return User
     */
    public function setIsSuperAdmin($isSuperAdmin)
    {
        if ($isSuperAdmin) {
            if (!$this->hasRole('ROLE_SUPER_ADMIN')) {
                $this->addRole('ROLE_SUPER_ADMIN');
            }
        } else {
            $this->deleteRole('ROLE_SUPER_ADMIN');
        }
        return $this;
    }

    /**
     * return boolean
     */
    public function getIsSuperAdmin()
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }
}
