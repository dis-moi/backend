<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Domain.
 *
 * @ORM\Table(name="domain_name")
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class DomainName
{
    use ORMBehaviors\Timestampable\Timestampable;

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
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
     *   joinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     *   )
     */
    private $matchingContexts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="DomainsSet", mappedBy="domains")
     * @ORM\JoinTable(name="domains_set_domain",
     *   joinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     *   )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $sets;

    /**
     * Domain constructor.
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->matchingContexts = new ArrayCollection();
        $this->sets = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getPrettyName();
    }

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
     * @return DomainName
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNotices()
    {
        return array_map(function (MatchingContext $mc) {
            return $mc->getNotice();
        }, $this->getMatchingContexts()->toArray());
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): DomainName
    {
        $this->path = $path;

        return $this;
    }
}
