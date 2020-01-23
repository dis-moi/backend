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
   * @var ExtensionUser
   *
   * @ORM\ManyToOne(targetEntity=ExtensionUser::class, inversedBy="subscriptions", cascade={"persist"}, fetch="EAGER")
   * @ORM\JoinColumn(nullable=false)
   * @ORM\Id
   */
  private $extensionUser;

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

  public function __construct(Contributor $contributor, ExtensionUser $extensionUser)
  {
    $this->created = new DateTime();
    $this->contributor = $contributor;
    $this->extensionUser = $extensionUser;
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
   * @return ExtensionUser
   */
  public function getExtensionUser(): ExtensionUser
  {
    return $this->extensionUser;
  }
}
