<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Extension;
use Doctrine\ORM\EntityManagerInterface;

class ExtensionRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function getClassName()
    {
        return Extension::class;
    }

    public function findOrCreate(string $id)
    {
        return parent::find($id) ?: new Extension($id);
    }
}
