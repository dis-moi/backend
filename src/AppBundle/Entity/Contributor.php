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
     * @ORM\Column(name="role", type="string", options={"default" : "author"})
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="contributors", cascade={"persist"})
     */
    private $organization;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="contributor", cascade={"persist","remove"}, fetch="EAGER")
     */
    private $user;

    /**
     * @var datetime $updatedAt
     * Needed to trigger forced update when an avatar is uploaded
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Recommendation", mappedBy="contributor")
     */
    private $recommendations;

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
     * @param \AppBundle\Entity\Organization $organization
     *
     * @return Contributor
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    public function __toString()
    {
        return $this->organization.' | '.$this->name.' | '.$this->role;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recommendations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->role = ContributorRole::getDefault();
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Contributor
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add recommendation
     *
     * @param \AppBundle\Entity\Recommendation $recommendation
     *
     * @return Contributor
     */
    public function addRecommendation(\AppBundle\Entity\Recommendation $recommendation)
    {
        $this->recommendations[] = $recommendation;

        return $this;
    }

    /**
     * Remove recommendation
     *
     * @param \AppBundle\Entity\Recommendation $recommendation
     */
    public function removeRecommendation(\AppBundle\Entity\Recommendation $recommendation)
    {
        $this->recommendations->removeElement($recommendation);
    }

    /**
     * Get recommendations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecommendations()
    {
        return $this->recommendations;
    }

    /**
     * @return ContributorRole
     */
    public function getRole()
    {
        if (!$this->role) {
            return ContributorRole::getDefault();
        }
        return ContributorRole::get($this->role);
    }

    /**
     * @param ContributorRole $role
     * @throw InvalidArgumentException
     * @return Contributor
     */
    public function setRole(ContributorRole $role)
    {
        $this->role = $role->getValue();

        return $this;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return Contributor
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
