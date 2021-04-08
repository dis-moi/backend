<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @throws NonUniqueResultException
     *
     * @return Subscription?
     */
    public function findOne(string $extensionId, int $contributorId)
    {
        return $this->createQueryBuilder('s')
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
