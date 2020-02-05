<?php


namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Entity
 * @ORM\Table(name="subscription")
 */
class Subscription
{
  const DAYS_TO_BE_CONSIDERED_ACTIVE = 15;

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
   * @var DateTime $updated
   *
   * @ORM\Column(type="datetime")
   */
  private $updated;

  /**
   * @var DateTime $created
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

  public function confirm()
  {
    $this->updated = new DateTime('now');
  }

  /**
   * @return Contributor
   */
  public function getContributor(): Contributor
  {
    return $this->contributor;
  }

  /**
   * @return Extension
   */
  public function getExtension(): Extension
  {
    return $this->extension;
  }

  /**
   * @return DateTime
   */
  public function getCreated(): DateTime
  {
    return $this->created;
  }

  /**
   * @return DateTime
   */
  public function getUpdated(): DateTime
  {
    return $this->updated;
  }

  public function isActive(): bool
  {
    $now = new DateTime('now');
    return (
      $this->created->diff($now, TRUE)->days < self::DAYS_TO_BE_CONSIDERED_ACTIVE
      ||
      $this->updated->diff($now, TRUE)->days < self::DAYS_TO_BE_CONSIDERED_ACTIVE
    );
  }

  public static function getFreshnessDate(): DateTime
  {
    return new DateTime('-'.self::DAYS_TO_BE_CONSIDERED_ACTIVE.'days');
  }
}
