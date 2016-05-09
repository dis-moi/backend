<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alternative
 *
 * @ORM\Table(name="alternative")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecommendationRepository")
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
     * @ORM\ManyToOne(targetEntity="Recommendation", inversedBy="alternatives")
     */
    private $recommendation;

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
     * Set recommendation
     *
     * @param \AppBundle\Entity\Recommendation $recommendation
     *
     * @return Alternative
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
        return (is_null($this->getLabel())) ? 'you must set a label' : $this->getLabel();
    }
}
