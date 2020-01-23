<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class SubscriptionRepository extends BaseRepository
{
  public function __construct(EntityManagerInterface $entityManager)
  {
    parent::__construct($entityManager);
  }

  public function getResourceClassName()
  {
    return Subscription::class;
  }

  /**
   * @param string $extensionUserId
   * @param string $contributorId
   * @return Subscription?
   * @throws NonUniqueResultException
   */
  public function findOne(string $extensionUserId, string $contributorId)
  {
//    $criteria = new Criteria();
//    $criteria->where($criteria->expr()->eq('contributor', $contributorId));
//
//    return $this->repository->matching($criteria)->first();

    $query = $this->repository->createQueryBuilder('s')
      ->select('s')
      ->leftJoin('s.contributor', 'contributor')
      ->where('s.extensionUser = :extensionUserId')
      ->andWhere('s.contributor = :contributorId')
      ->setParameter('extensionUserId', $extensionUserId)
      ->setParameter('contributorId', $contributorId)
      ->getQuery();

    return $query->getOneOrNullResult();
  }
}
