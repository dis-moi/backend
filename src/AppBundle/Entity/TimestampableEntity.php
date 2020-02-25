<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class TimestampableEntity
{
  /**
   * @var DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  protected $createdAt;

  /**
   * @var DateTime
   *
   * @ORM\Column(name="updatedAt", type="datetime")
   */
  protected $updatedAt;

  /**
   * @ORM\PrePersist
   * @ORM\PreUpdate
   */
  public function updateTimestamps(): void
  {
    $now = new DateTime('now');
    $this->updatedAt = $now;
    if ($this->createdAt === null)
    {
      $this->createdAt = $now;
    }
  }

  /**
   * Get createdAt.
   *
   * @return DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Get updatedAt.
   *
   * @return DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }
}
