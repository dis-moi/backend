<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchingContext
 *
 * @ORM\Table(name="matching_context")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatchingContextRepository")
 */
class MatchingContext
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
     * @ORM\ManyToOne(targetEntity="Recommendation")
     */
    private $recommendation;

    /**
     * @var string
     *
     * @ORM\Column(name="urlRegex", type="string", length=255)
     */
    private $urlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expirationDate", type="datetime", nullable=true)
     */
    private $expirationDate;


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
     * Set urlRegex
     *
     * @param string $urlRegex
     *
     * @return MatchingContext
     */
    public function setUrlRegex($urlRegex)
    {
        $this->urlRegex = $urlRegex;

        return $this;
    }

    /**
     * Get urlRegex
     *
     * @return string
     */
    public function getUrlRegex()
    {
        return $this->urlRegex;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MatchingContext
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
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return MatchingContext
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set recommendation
     *
     * @param \AppBundle\Entity\Recommendation $recommendation
     *
     * @return MatchingContext
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
}
