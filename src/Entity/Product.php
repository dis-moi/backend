<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Any offered product or service. For example: a pair of shoes; a concert ticket; the rental of a car; a haircut; or an episode of a TV show streamed online.
 *
 * @see http://schema.org/Product Documentation on Schema.org
 *
 * @ORM\Entity
 * @ApiResource(
 *     iri="http://schema.org/Product",
 *     normalizationContext={
 *         "groups"={"read"}
 *     }
 * )
 */
class Product extends Thing
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.
     *
     * @ORM\Column(type="text", nullable=true)
     * @ApiProperty(iri="http://schema.org/category")
     * @Groups({
     *     "read"
     * })
     */
    private $category;

    /**
     * @var Offer|null an offer to provide this item—for example, an offer to sell a product, rent the DVD of a movie, perform a service, or give away tickets to an event
     *
     * @ORM\Embedded(class="App\Entity\Offer", columnPrefix="offer_")
     * @ApiProperty(iri="http://schema.org/offers")
     * @Groups({
     *     "read"
     * })
     */
    private $offer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setOffer(?Offer $offer): void
    {
        $this->offer = $offer;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }
}
