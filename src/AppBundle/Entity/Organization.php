<?php
// src/AppBundle/Entity/Organization.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="organization")
 */
class Organization
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Contributor", mappedBy="organization", cascade={"persist"})
     */
    private $contributors;


    public function __construct()
    {
        $this->contributors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Organization
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Organization
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Add contributor
     *
     * @param \AppBundle\Entity\Contributor $contributor
     *
     * @return Recommendation
     */
    public function addContributor(\AppBundle\Entity\Contributor $contributor)
    {
        $contributor->setOrganization($this);

        $this->contributors[] = $contributor;

        return $this;
    }

    /**
     * Remove contributor
     *
     * @param \AppBundle\Entity\Contributor $contributor
     */
    public function removeContributor(\AppBundle\Entity\Contributor $contributor)
    {
        $this->contributors->removeElement($contributor);
    }

    /**
     * Get contributor
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributors()
    {
        return $this->contributors;
    }


    public function __toString()
    {
        return $this->description.' | '.$this->name;
    }
}
