<?php


namespace AppBundle\Repository;


use AppBundle\Entity\ExtensionUser;
use Doctrine\ORM\EntityManagerInterface;

class ExtensionUserRepository extends BaseRepository
{
  public function __construct(EntityManagerInterface $entityManager)
  {
    parent::__construct($entityManager);
  }

  public function getResourceClassName()
  {
    return ExtensionUser::class;
  }

  public function findOrCreate(string $id)
  {
    return parent::find($id) ?: new ExtensionUser($id);
  }
}
