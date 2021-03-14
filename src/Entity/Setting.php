<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Setting.
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var string
     *
     * @ORM\Column(name="k", type="string", length=20)
     * @ORM\Id
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="v", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $value;

    public function __construct(string $key, string $value)
    {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * Set key.
     */
    public function setKey(string $key): Setting
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get id.
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Set value.
     */
    public function setValue(string $value): Setting
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}
