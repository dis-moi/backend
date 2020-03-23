<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Escaper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\EntityListener\MatchingContextListener;

function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}

function escape(string $dn, ?Escaper $e) {
  return is_null($e) ? $dn : $e::escape($dn);
}


/**
 * MatchingContext
 *
 * @ORM\Table(name="matching_context")
 * @ORM\Entity
 * @ORM\EntityListeners({MatchingContextListener::class})
 */
class MatchingContext
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
     * @var Notice
     *
     * @ORM\ManyToOne(targetEntity="Notice", inversedBy="matchingContexts", fetch="EAGER")
     */
    private $notice;

    /**
     * @var string
     *
     * @ORM\Column(name="exampleUrl", type="text", nullable=true)
     *
     * @Assert\Url
     */
    private $exampleUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="domainName", type="string", length=150, nullable=true)
     *
     * @Assert\Regex("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/")
     */
    private $domainName;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="DomainName", inversedBy="matchingContexts", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="matching_context_domain_name",
     *   joinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     *   )
     */
    private $domainNames;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="DomainsSet", inversedBy="matchingContexts", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="domains_set_domain",
     *   joinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")}
     *   )
     */
    private $domainsSets;

    /**
     * @var string
     *
     * @ORM\Column(name="urlRegex", type="text")
     *
     * @Assert\NotBlank()
     */
    private $urlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="excludeUrlRegex", type="text", nullable=true)
     */
    private $excludeUrlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="querySelector", type="string", length=255, nullable=true)
     */
    private $querySelector;

    public function __construct()
    {
        $this->urlRegex = '';
        $this->domainNames = new ArrayCollection();
        $this->domainsSets = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setExampleUrl(string $exampleUrl = null) : MatchingContext
    {
        $this->exampleUrl = $exampleUrl;

        return $this;
    }

    public function getExampleUrl() : ?string
    {
        return $this->exampleUrl;
    }

    public function setDomainName(string $domainName = null) : MatchingContext
    {
        $this->domainName = $domainName;

        return $this;
    }

    public function getDomainName() : ?string
    {
        return $this->domainName;
    }

    /**`
     * Set urlRegex
     *
     * @param string $urlRegex
     *
     * @return MatchingContext
     */
    public function setUrlRegex($urlRegex) : self
    {
        $this->urlRegex = $urlRegex;

        return $this;
    }

    /**
     * Get urlRegex
     *
     * @return string
     */
    public function getUrlRegex() : string
    {
        return $this->urlRegex;
    }

    /**
     * @param Escaper|null $escaper
     * @return string
     */
    public function getFullUrlRegex(Escaper $escaper = null) : string
    {
        $domains = $this->getAllRelatedDomains();
        if (count($domains) === 0) {
            return $this->urlRegex;
        }

        return '('.join('|',
          array_map(function (DomainName $dn) use ($escaper) {
            return escape($dn->getName(), $escaper);
          }, $domains)
        ).')'.$this->urlRegex;
    }

    /**
     * @param null|string $excludeUrlRegex
     *
     * @return MatchingContext
     */
    public function setExcludeUrlRegex($excludeUrlRegex) : self
    {
        $this->excludeUrlRegex = $excludeUrlRegex;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExcludeUrlRegex() : ?string
    {
        return $this->excludeUrlRegex;
    }

    /**
     * @return null|string
     */
    public function getCompleteExcludeUrlRegex() : ?string
    {
        $noticeExcludeRegex = $this->getNotice()->getExcludeUrlRegex();
        $mcExcludeRegex = $this->getExcludeUrlRegex();

        if ($noticeExcludeRegex && $mcExcludeRegex)
        {
            return '('.$mcExcludeRegex.'|'.$noticeExcludeRegex.')';
        }

        return $mcExcludeRegex ?? $noticeExcludeRegex;
    }

    /**
     * Set description
     *
     * @param null|string $description
     *
     * @return MatchingContext
     */
    public function setDescription($description) : self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return null|string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Set notice
     *
     * @param Notice $notice
     *
     * @return MatchingContext
     */
    public function setNotice(Notice $notice = null) : self
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return Notice
     */
    public function getNotice() : Notice
    {
        return $this->notice;
    }

    public function __toString() : string
    {
        return (is_null($this->getDescription())) ? 'you must set a description' : $this->getDescription();
    }

    /**
     * @return string?
     */
    public function getQuerySelector() : ?string
    {
        return $this->querySelector;
    }

    /**
     * @param string $querySelector
     * @return MatchingContext
     */
    public function setQuerySelector($querySelector) : self
    {
        $this->querySelector = $querySelector;

        return $this;
    }


    /**
     * @return Collection
     */
    public function getDomainNames(): Collection
    {
        return $this->domainNames;
    }

    public function addDomainName(DomainName $domainName) : self
    {
        $this->domainNames[] = $domainName;

        return $this;
    }

    public function removeDomainName(DomainName $domainName) : self
    {
        if ($this->domainNames->contains($domainName)) {
          $this->domainNames->removeElement($domainName);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDomainsSets(): Collection
    {
        return $this->domainsSets;
    }

    public function addDomainsSet(DomainsSet $domainsSet) : self
    {
        $this->domainsSets[] = $domainsSet;

        return $this;
    }

    public function removeDomainsSet(DomainsSet $domainsSet) : self
    {
        if ($this->domainsSets->contains($domainsSet)) {
            $this->domainsSets->removeElement($domainsSet);
        }

        return $this;
    }

    /**
     * @return DomainName[]
     */
    public function getAllRelatedDomains() : array
    {
        return array_unique(array_merge(
            flatten(array_map(
                function (DomainsSet $domainsSet) {
                    return $domainsSet->getDomains()->toArray();
                },
                $this->getDomainsSets()->toArray()
            )),
            $this->getDomainNames()->toArray()
        ));
    }
}
