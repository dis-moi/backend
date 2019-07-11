<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Contributor
 *
 * @ORM\Table(name="contributor")
 * @ORM\Entity
 */
class Contributor
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"v3:list"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Groups({"v3:list"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="string", length=255, nullable=true)
     *
     * @Groups({"v3:list"})
     */
    private $intro;

    /**
     * @ORM\OneToMany(targetEntity="Notice", mappedBy="contributor")
     */
    private $notices;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notices = new ArrayCollection();
    }

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

    public function setIntro(?string $intro) : Contributor
    {
        $this->intro = $intro;

        return $this;
    }

    public function getIntro() : ?string
    {
        return $this->intro;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add notice
     *
     * @param Notice $notice
     *
     * @return Contributor
     */
    public function addNotice(Notice $notice)
    {
        $this->notices[] = $notice;

        return $this;
    }

    /**
     * Remove notice
     *
     * @param Notice $notice
     */
    public function removeNotice(Notice $notice)
    {
        $this->notices->removeElement($notice);
    }

    public function getNotices() : ?Collection
    {
        return $this->notices;
    }

    public function getNoticesCount() : int
    {
        if ($notices = $this->getNotices()) {
            return $notices
                ->filter(function (Notice $notice) {
                    return $notice->hasPublicVisibility();
                })
                ->count();
        }
        else return 0;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}
