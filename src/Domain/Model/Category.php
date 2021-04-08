<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Doctrine\Common\Inflector\Inflector;

/**
 * A contributor's category.
 * A contributor may appears in zero, one or many categories.
 */
class Category
{
    /**
     * A category must have a name, might be used as an hashtag later on.
     *
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHashtag(): string
    {
        return ucfirst(Inflector::camelize($this->getName()));
    }

    public function __toString()
    {
        return '#'.$this->getHashtag();
    }

    public static function createFromName(string $name): self
    {
        $category = new self();
        $category->setName($name);

        return $category;
    }
}
