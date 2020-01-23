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
}
