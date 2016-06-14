<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Contributor
 *
 * @ORM\Table(name="contributor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContributorRepository")
 * @Vich\Uploadable
 */
class Contributor
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
     * @ORM\Column(name="organization", type="string", length=255)
     */
    private $organization;

    /**
     * @var datetime $updatedAt
     * Needed to trigger forced update when an avatar is uploaded
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="Recommendation")
     */
    private $recommendation;

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

    /**
     * Set organization
     *
     * @param string $organization
     *
     * @return Contributor
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="contributor_avatars", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
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

    /**
     * Set recommendation
     *
     * @param \AppBundle\Entity\Recommendation $recommendation
     *
     * @return Contributor
     */
    public function setRecommendation(\AppBundle\Entity\Recommendation $recommendation = null)
    {
        $this->recommendation = $recommendation;

        return $this;
    }

    /**
     * Get recommendation
     *
     * @return \AppBundle\Entity\Recommendation
     */
    public function getRecommendation()
    {
        return $this->recommendation;
    }

    public function __toString()
    {
        return $this->organization.' | '.$this->name;
    }
}

