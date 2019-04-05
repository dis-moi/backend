<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Intention
 *
 * @ORM\Table(name="notice_intention")
 * @ORM\Entity
 */
class Intention
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
     * @ORM\Column(name="label", type="string", length=255)
     *
     * @Groups({"v3:list"})
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     *
     * @Groups({"v3:list"})
     */
    private $slug;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function setLabel(string $label) : Intention
    {
        $this->label = $label;
        if (empty($this->slug)) {
            $this->slug = $this->slugify($this->label);
        }
        return $this;
    }

    public function getLabel() : ?string
    {
        return $this->label;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function __toString() : string
    {
        return $this->label;
    }

    private function slugify(string $text) : string
    {
        $text = preg_replace('/\W+/', '-', $text);
        $text = strtolower(trim($text, '-'));
        return $text;
    }

    public function setSlug(string $slug) : Intention
    {
        $this->slug = $slug;

        return $this;
    }
}
