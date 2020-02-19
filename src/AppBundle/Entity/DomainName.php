<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Domain
 *
 * @ORM\Table(name="domain_name")
 * @ORM\Entity
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

  /**
   * @var Collection
   *
   * @ORM\ManyToMany(targetEntity="MatchingContext", mappedBy="domainNames", cascade={"persist", "remove"})
   * @ORM\JoinTable(name="matching_context_domain_name",
   *   joinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")}
   *   )
   */
  private $matchingContexts;

  /**
   * @var Collection
   *
   * @ORM\ManyToMany(targetEntity="DomainsSet", mappedBy="domains", cascade={"persist", "remove"})
   * @ORM\JoinTable(name="domains_set_domain",
   *   joinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")}
   *   )
   */
  private $sets;

  /**
   * Domain constructor.
   * @param string $name
   */
  public function __construct(string $name = "")
  {
    $this->name = $name;
    $this->matchingContexts = new ArrayCollection();
    $this->sets = new ArrayCollection();
  }

  /**
   * @return string
   */
  public function __toString() : string
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
  public function getName() : string
  {
    return $this->name;
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
  public function getSets(): Collection
  {
    return $this->sets;
  }

  /**
   * @param string $name
   * @return DomainName
   */
  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }
}
