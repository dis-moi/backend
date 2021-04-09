<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Domain.
 *
 * @ORM\Table(name="domains_set")
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class DomainsSet
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
     */
    private $name;

    /**
     * @var Collection<MatchingContext>
     *
     * @ORM\ManyToMany(targetEntity="MatchingContext", mappedBy="domainsSets")
     * @ORM\JoinTable(name="matching_context_domains_set",
     *     joinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")}
     * )
     */
    private $matchingContexts;

    /**
     * @var Collection<DomainName>
     *
     * @ORM\ManyToMany(targetEntity="DomainName", inversedBy="sets")
     * @ORM\JoinTable(name="domains_set_domain",
     *     joinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $domains;

    /**
     * Domain constructor.
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->matchingContexts = new ArrayCollection();
        $this->domains = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
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

    /**
     * @return DomainsSet
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function addDomain(DomainName $domain): self
    {
        $this->domains[] = $domain;

        return $this;
    }

    public function getMatchingContexts(): Collection
    {
        return $this->matchingContexts;
    }

    public function getDomains(): Collection
    {
        return $this->domains;
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
}
