<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Domain.
 *
 * @ORM\Table(name="domain_name")
 * @ORM\Entity
 */
class DomainName
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @see https://github.com/doctrine/orm/issues/4673
     */
    public const EMPTY_SIMPLE_ARRAY = [''];

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=false)
     * @Assert\Regex("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/")
     */
    private $name;

    /** @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     * @Assert\Regex("/^\/([\w\d]+[-_%.\/\w\d]*)?$/")
     */
    private $path;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="MatchingContext", mappedBy="domainNames")
     * @ORM\JoinTable(name="matching_context_domain_name",
     *     joinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     * )
     */
    private $matchingContexts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="DomainsSet", mappedBy="domains")
     * @ORM\JoinTable(name="domains_set_domain",
     *     joinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $sets;

    /**
     * @var string[]
     *
     * @ORM\Column(type="simple_array")
     *
     * @Assert\All({
     *     @Assert\Regex("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/")
     * })
     */
    private $aliases;

    /**
     * Domain constructor.
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->matchingContexts = new ArrayCollection();
        $this->sets = new ArrayCollection();
        $this->aliases = self::EMPTY_SIMPLE_ARRAY;
    }

    public function __toString(): string
    {
        return $this->getPrettyName();
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getPrettyName(): string
    {
        return $this->getFullName();
    }

    public function getFullName(): string
    {
        return $this->name.$this->path;
    }

    public function getMatchingContexts(): Collection
    {
        return $this->matchingContexts;
    }

    public function getSets(): Collection
    {
        return $this->sets;
    }

    /**
     * @param string[] $aliases
     */
    public function setAliases(array $aliases = []): self
    {
        $this->aliases = array_filter($aliases, static function ($alias) {
            return '' !== $alias;
        }) ?: self::EMPTY_SIMPLE_ARRAY;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return array_filter($this->aliases, static function ($alias) {
            return '' !== $alias;
        });
    }

    /**
     * @return DomainName
     */
    public function addAlias(string $alias): self
    {
        $this->aliases = array_merge($this->aliases, [$alias]);

        return $this;
    }

    public function removeAlias(string $alias): self
    {
        $this->aliases = array_diff($this->aliases, [$alias]);

        return $this;
    }

    /**
     * @return DomainName
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Notice[]
     */
    public function getNotices(): array
    {
        return array_map(function (MatchingContext $mc) {
            return $mc->getNotice();
        }, $this->getMatchingContexts()->toArray());
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
