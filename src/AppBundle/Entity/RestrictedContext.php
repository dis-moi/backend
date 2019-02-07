<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RestrictedContext
 *
 * @ORM\Table(name="restricted_context")
 * @ORM\Entity
 */
class RestrictedContext
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
     * @ORM\Column(name="urlRegex", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $urlRegex;


    /**
     * Get id.
     *
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Set urlRegex.
     *
     */
    public function setUrlRegex(string $urlRegex) : RestrictedContext
    {
        $this->urlRegex = $urlRegex;

        return $this;
    }

    /**
     * Get urlRegex.
     *
     */
    public function getUrlRegex() : ?string
    {
        return $this->urlRegex;
    }
}
