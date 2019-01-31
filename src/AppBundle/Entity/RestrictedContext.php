<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\EntityListener\RestrictedContextListener;

/**
 * RestrictedContext
 *
 * @ORM\Table(name="restricted_context")
 * @ORM\Entity
 * @ORM\EntityListeners({RestrictedContextListener::class})
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set urlRegex.
     *
     * @param string $urlRegex
     *
     * @return RestrictedContext
     */
    public function setUrlRegex($urlRegex)
    {
        $this->urlRegex = $urlRegex;

        return $this;
    }

    /**
     * Get urlRegex.
     *
     * @return string
     */
    public function getUrlRegex()
    {
        return $this->urlRegex;
    }
}
