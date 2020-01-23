<?php


namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * Extension
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="extension")
 */
class Extension
{
  /**
   * @var string
   *
   * @ORM\Column(name="id", type="string")
   * @ORM\Id
   */
  private $id;

  /**
   * @var PersistentCollection
   * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="extension")
   * @ORM\OrderBy({"created" = "DESC"})
   */
  private $subscriptions;

  /**
   * @var DateTime $created
   *
   * @ORM\Column(type="datetime")
   */
  private $created;

  /**
   * @var DateTime $updated
   *
   * @ORM\Column(type="datetime")
   */
  private $updated;

  public function __construct(string $id) {

    $this->id = $id;
    $this->created = new DateTime();
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * @return PersistentCollection
   */
  public function getSubscriptions(): PersistentCollection
  {
    return $this->subscriptions;
  }

  public function confirm()
  {
    $this->updated = new DateTime('now');
  }
}
