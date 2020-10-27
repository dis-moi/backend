<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class SubscriptionRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function getClassName()
    {
        return Subscription::class;
    }

    /**
     * @return Subscription?
     *
     * @throws NonUniqueResultException
     */
    public function findOne(string $extensionId, string $contributorId)
    {
        return $this->repository->createQueryBuilder('s')
      ->select('s')
      ->leftJoin('s.contributor', 'contributor')
      ->where('s.extension = :extensionId')
      ->andWhere('s.contributor = :contributorId')
      ->setParameter('extensionId', $extensionId)
      ->setParameter('contributorId', $contributorId)
      ->getQuery()
      ->getOneOrNullResult();
    }
}
