<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Domain
 *
 * @ORM\Table(name="domains_set")
 * @ORM\Entity
 */
class DomainsSet extends TimestampableEntity
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
   * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
   * @Assert\Regex("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/")
   */
  private $name;

  /**
   * @var Collection
   *
   * @ORM\ManyToMany(targetEntity="MatchingContext", mappedBy="domainsSets", cascade={"persist", "remove"})
   * @ORM\JoinTable(name="matching_context_domains_set",
   *   joinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")}
   *   )
   */
  private $matchingContexts;

  /**
   * @var Collection
   *
   * @ORM\ManyToMany(targetEntity="DomainName", inversedBy="sets", cascade={"persist", "remove"})
   * @ORM\JoinTable(name="domains_set_domain",
   *   joinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
   *   )
   */
  private $domains;

  /**
   * Domain constructor.
   * @param string $name
   */
  public function __construct(string $name = "")
  {
    $this->name = $name;
    $this->matchingContexts = new ArrayCollection();
    $this->domains = new ArrayCollection();
  }

  public function __toString()
  {
    return $this->name;
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
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function addDomain(DomainName $domain)
  {
    $this->domains[] = $domain;
  }

  /**
   * @return Collection
   */
  public function getMatchingContexts(): Collection
  {
    return $this->matchingContexts;
  }
  /**
   * @return Collection
   */
  public function getDomains(): Collection
  {
    return $this->domains;
  }
}
