<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\EntityListener\MatchingContextListener;
use App\Helper\Escaper;
use Doctrine\Common\Collections\ArrayCollection;
use App\Serializer\V3\NormalizerOptions;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @param array<array<mixed>> $array
 *
 * @return array<mixed>
 */
function flatten(array $array): array
{
    $return = [];
    array_walk_recursive($array, function ($a) use (&$return): void {
        $return[] = $a;
    });

    return $return;
}

function escape(string $dn, ?Escaper $e): string
{
    return null === $e ? $dn : $e::escape($dn);
}

/**
 * A MatchingContext pinpoints a set of web pages on the internet.
 * A MatchingContext can also pinpoint to parts of them, though xpath.
 * A MatchingContext exists for a single Notice.
 *
 * @ORM\Table(name="matching_context")
 * @ORM\Entity
 * @ORM\EntityListeners({MatchingContextListener::class})
 * @ApiResource(
 *     normalizationContext={
 *         "groups"={"read"},
 *         NormalizerOptions::VERSION=4,
 *     },
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"read"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *         },
 *     },
 *     collectionOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"read"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *         },
 *         "post"={
 *             "denormalization_context"={
 *                 "groups"={"create"},
 *                 NormalizerOptions::VERSION=4,
 *             },
 *             "access_control"="is_granted('can_create', object)",
 *         },
 *     },
 * )
 */
class MatchingContext
{
    /**
     * An incremental, unique identifier for this matching context.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "create",
     * })
     * @ApiProperty(
     *     openapiContext={
     *         "example"=42,
     *     },
     * )
     */
    private $id;

    /**
     * This Notice this Matching Context tries to pinpoint on the Internet.
     *
     * @var Notice
     *
     * @ORM\ManyToOne(targetEntity="Notice", inversedBy="matchingContexts", fetch="EAGER")
     * @Groups({
     *     "create",
     *     "read",
     * })
     */
    private $notice;

    /**
     * @var string
     *
     * @ORM\Column(name="exampleUrl", type="text", nullable=true)
     *
     * @Assert\Url
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $exampleUrl;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="DomainName", inversedBy="matchingContexts", cascade={"persist"})
     * @ORM\JoinTable(name="matching_context_domain_name",
     *     joinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="domain_name_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $domainNames;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="DomainsSet", inversedBy="matchingContexts")
     * @ORM\JoinTable(name="matching_context_domains_set",
     *     joinColumns={@ORM\JoinColumn(name="matching_context_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="domains_set_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $domainsSets;

    /**
     * @var string
     *
     * @ORM\Column(name="urlRegex", type="text")
     *
     * @Assert\NotBlank
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $urlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="excludeUrlRegex", type="text", nullable=true)
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $excludeUrlRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $description;

    /**
     * @var string a CSS selector
     *
     * @ORM\Column(name="querySelector", type="string", length=255, nullable=true)
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $querySelector;

    /**
     * @var string an XPath expression
     *
     * @ORM\Column(name="xpath", type="text", nullable=true)
     * @Groups({
     *     "create",
     *     "read",
     *     "update",
     * })
     */
    private $xpath;

    public function __construct()
    {
        $this->urlRegex = '';
        $this->domainNames = new ArrayCollection();
        $this->domainsSets = new ArrayCollection();
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setExampleUrl(string $exampleUrl = null): self
    {
        $this->exampleUrl = $exampleUrl;

        return $this;
    }

    public function getExampleUrl(): ?string
    {
        return $this->exampleUrl;
    }

    /** `
     * Set urlRegex.
     *
     * @return MatchingContext
     */
    public function setUrlRegex(string $urlRegex): self
    {
        $this->urlRegex = $urlRegex;

        return $this;
    }

    /**
     * Get urlRegex.
     */
    public function getUrlRegex(): string
    {
        return $this->urlRegex;
    }

    public function getFullUrlRegex(Escaper $escaper = null): string
    {
        $domains = $this->getAllRelatedDomains();
        if (0 === \count($domains)) {
            return $this->urlRegex;
        }

        return '('.implode(
            '|',
            array_map(static function (DomainName $dn) use ($escaper) {
                return escape($dn->getFullName(), $escaper);
            }, $domains)
        ).')'.$this->urlRegex;
    }

    /**
     * @return MatchingContext
     */
    public function setExcludeUrlRegex(?string $excludeUrlRegex): self
    {
        $this->excludeUrlRegex = $excludeUrlRegex;

        return $this;
    }

    public function getExcludeUrlRegex(): ?string
    {
        return $this->excludeUrlRegex;
    }

    public function getCompleteExcludeUrlRegex(): ?string
    {
        $noticeExcludeRegex = $this->getNotice()->getExcludeUrlRegex();
        $mcExcludeRegex = $this->getExcludeUrlRegex();

        if ($noticeExcludeRegex && $mcExcludeRegex) {
            return '('.$mcExcludeRegex.'|'.$noticeExcludeRegex.')';
        }

        return $mcExcludeRegex ?? $noticeExcludeRegex;
    }

    /**
     * Set description.
     *
     * @return MatchingContext
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set notice.
     *
     * @param Notice $notice
     *
     * @return MatchingContext
     */
    public function setNotice(Notice $notice = null): self
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice.
     */
    public function getNotice(): Notice
    {
        return $this->notice;
    }

    public function __toString(): string
    {
        return (null === $this->getDescription()) ? 'you must set a description' : $this->getDescription();
    }

    /**
     * @return string?
     */
    public function getQuerySelector(): ?string
    {
        return $this->querySelector;
    }

    /**
     * @return MatchingContext
     */
    public function setQuerySelector(string $querySelector): self
    {
        $this->querySelector = $querySelector;

        return $this;
    }

    /**
     * @return string?
     */
    public function getXpath(): ?string
    {
        return $this->xpath;
    }

    public function setXpath(string $xpath): void
    {
        $this->xpath = $xpath;
    }

    public function getDomainNames(): Collection
    {
        return $this->domainNames;
    }

    public function addDomainName(DomainName $domainName): self
    {
        $this->domainNames[] = $domainName;

        return $this;
    }

    public function removeDomainName(DomainName $domainName): self
    {
        if ($this->domainNames->contains($domainName)) {
            $this->domainNames->removeElement($domainName);
        }

        return $this;
    }

    public function getDomainsSets(): Collection
    {
        return $this->domainsSets;
    }

    public function addDomainsSet(DomainsSet $domainsSet): self
    {
        $this->domainsSets[] = $domainsSet;

        return $this;
    }

    public function removeDomainsSet(DomainsSet $domainsSet): self
    {
        if ($this->domainsSets->contains($domainsSet)) {
            $this->domainsSets->removeElement($domainsSet);
        }

        return $this;
    }

    /**
     * @return DomainName[]
     */
    public function getAllRelatedDomains(): array
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
