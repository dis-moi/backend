<?php


namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * ExtensionUser
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="extension_user")
 */
class ExtensionUser
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
   * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="extensionUser")
   * @ORM\OrderBy({"created" = "DESC"})
   */
  private $subscriptions;

  /**
   * @var DateTime $created
   *
   * @ORM\Column(type="datetime")
   */
  private $created;

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
}
