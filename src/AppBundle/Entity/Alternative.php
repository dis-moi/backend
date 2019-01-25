<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alternative
 *
 * @ORM\Table(name="alternative")
 * @ORM\Entity
 *
 * @deprecated since API v3, will be removed soon
 */
class Alternative
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
     * @ORM\ManyToOne(targetEntity="Notice", inversedBy="alternatives")
     * @ORM\JoinColumn(name="recommendation_id", referencedColumnName="id")
     */
    private $notice;

    /**
     * @var string
     *
     * @ORM\Column(name="urlToRedirect", type="string", length=255)
     */
    private $urlToRedirect;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;


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
     * Set urlToRedirect
     *
     * @param string $urlToRedirect
     *
     * @return Alternative
     */
    public function setUrlToRedirect($urlToRedirect)
    {
        $this->urlToRedirect = $urlToRedirect;

        return $this;
    }

    /**
     * Get urlToRedirect
     *
     * @return string
     */
    public function getUrlToRedirect()
    {
        return $this->urlToRedirect;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Alternative
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Alternative
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notice
     *
     * @param Notice $notice
     *
     * @return Alternative
     */
    public function setNotice(Notice $notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return Notice
     */
    public function getNotice()
    {
        return $this->notice;
    }

    public function __toString()
    {
        return (is_null($this->getLabel())) ? 'you must set a label' : $this->getLabel();
    }
}
