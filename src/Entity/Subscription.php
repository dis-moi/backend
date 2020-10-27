<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Subscription.
 *
 * @ORM\Entity
 * @ORM\Table(name="subscription")
 */
class Subscription
{
    public const DAYS_TO_BE_CONSIDERED_ACTIVE = 7;

    /**
     * @var Contributor
     *
     * @ORM\ManyToOne(targetEntity=Contributor::class, inversedBy="subscriptions", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $contributor;

    /**
     * @var Extension
     *
     * @ORM\ManyToOne(targetEntity=Extension::class, inversedBy="subscriptions", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $extension;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct(Contributor $contributor, Extension $extension)
    {
        $this->created = new DateTime();
        $this->contributor = $contributor;
        $this->extension = $extension;
    }

    public function confirm(): void
    {
        $this->updated = new DateTime('now');
    }

    public function getContributor(): Contributor
    {
        return $this->contributor;
    }

    public function getExtension(): Extension
    {
        return $this->extension;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function isActive(): bool
    {
        $now = new DateTime('now');

        return
      $this->created->diff($now, true)->days < self::DAYS_TO_BE_CONSIDERED_ACTIVE
      ||
      $this->updated->diff($now, true)->days < self::DAYS_TO_BE_CONSIDERED_ACTIVE
    ;
    }

    public static function getFreshnessDate(): DateTime
    {
        try {
            return new DateTime('-'.self::DAYS_TO_BE_CONSIDERED_ACTIVE.'days');
        } catch (Exception $e) {
            return new DateTime();
        }
    }
}
