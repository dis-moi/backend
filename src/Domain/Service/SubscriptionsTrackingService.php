<?php


namespace Domain\Service;


use AppBundle\Entity\Contributor;
use AppBundle\Entity\ExtensionUser;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ExtensionUserRepository;
use AppBundle\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use DomainException;

class SubscriptionsTrackingService
{
  /**
   * @var SubscriptionRepository
   */
  protected $subscriptionRepository;

  /**
   * @var ExtensionUserRepository
   */
  protected $extensionUserRepository;

  /**
   * @var ContributorRepository
   */
  private $contributorRepository;

  /**
   * @var EntityManagerInterface
   */
  private $entityManager;

  public function __construct(SubscriptionRepository $subscriptionRepository, ExtensionUserRepository $extensionUserRepository, ContributorRepository $contributorRepository, EntityManagerInterface $entityManager)
  {
    $this->subscriptionRepository = $subscriptionRepository;
    $this->extensionUserRepository = $extensionUserRepository;
    $this->contributorRepository = $contributorRepository;
    $this->entityManager = $entityManager;
  }

  /**
   * @param string $extensionUserId
   * @param string[] $contributorIds
   * @throws NonUniqueResultException
   */
  public function refreshSubscriptions(string $extensionUserId, array $contributorIds)
  {
    /**
     * @var ExtensionUser $extensionUser
     */
    if ($extensionUser = $this->extensionUserRepository->find($extensionUserId))
    {
      $existingSubscriptions = $extensionUser->getSubscriptions();
      foreach ($existingSubscriptions as $existingSubscription)
      {
        if (!in_array($existingSubscription->getContributor()->getId(), $contributorIds))
        {
          $this->entityManager->remove($existingSubscription);
        }
      }
    }

    foreach ($contributorIds as $contributorId)
    {
      /**
       * @var Contributor $contributor
       */
      $contributor = $this->contributorRepository->find($contributorId);
      if (!$contributor)
      {
        throw new DomainException("Contributor $contributorId does not exist");
      }

      /**
       * @var Subscription
       */
      $subscription = $this->subscriptionRepository->findOne($extensionUserId, $contributorId);
      if ($subscription)
      {
        $subscription->confirm();
      }
      else
      {
        $extensionUser = $this->extensionUserRepository->findOrCreate($extensionUserId);

        $subscription = new Subscription($contributor, $extensionUser);
        $this->entityManager->persist($subscription);
      }
    }

    $this->entityManager->flush();
  }
}
