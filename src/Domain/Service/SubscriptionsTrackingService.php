<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Entity\Contributor;
use App\Entity\Subscription;
use App\Repository\ContributorRepository;
use App\Repository\ExtensionRepository;
use App\Repository\SubscriptionRepository;
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
     * @var ExtensionRepository
     */
    protected $extensionRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(SubscriptionRepository $subscriptionRepository, ExtensionRepository $extensionRepository, ContributorRepository $contributorRepository, EntityManagerInterface $entityManager)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->extensionRepository = $extensionRepository;
        $this->contributorRepository = $contributorRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param int[] $contributorIds
     *
     * @throws NonUniqueResultException
     */
    public function refreshSubscriptions(string $extensionId, array $contributorIds): void
    {
        $extension = $this->extensionRepository->findOrCreate($extensionId);
        $extension->confirm();
        $existingSubscriptions = $extension->getSubscriptions();
        foreach ($existingSubscriptions as $existingSubscription) {
            if ( ! \in_array($existingSubscription->getContributor()->getId(), $contributorIds, true)) {
                $this->entityManager->remove($existingSubscription);
            }
        }

        foreach ($contributorIds as $contributorId) {
            /** @var Contributor|null $contributor */
            $contributor = $this->contributorRepository->find($contributorId);
            if ( ! $contributor) {
                throw new DomainException("Contributor $contributorId does not exist");
            }

            /** @var Subscription|null $subscription */
            $subscription = $this->subscriptionRepository->findOne($extensionId, $contributorId);
            if ($subscription) {
                $subscription->confirm();
            } else {
                $subscription = new Subscription($contributor, $extension);
                $subscription->confirm();
                $this->entityManager->persist($subscription);
            }
        }

        $this->entityManager->flush();
    }
}
