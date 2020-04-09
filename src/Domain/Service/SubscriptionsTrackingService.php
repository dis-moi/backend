<?php

namespace Domain\Service;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\Extension;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ExtensionRepository;
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
     * @param string[] $contributorIds
     *
     * @throws NonUniqueResultException
     */
    public function refreshSubscriptions(string $extensionId, array $contributorIds)
    {
        /**
         * @var Extension
         */
        if ($extension = $this->extensionRepository->find($extensionId)) {
            $extension->confirm();
            $existingSubscriptions = $extension->getSubscriptions();
            foreach ($existingSubscriptions as $existingSubscription) {
                if (!in_array($existingSubscription->getContributor()->getId(), $contributorIds)) {
                    $this->entityManager->remove($existingSubscription);
                }
            }
        }

        foreach ($contributorIds as $contributorId) {
            /**
             * @var Contributor
             */
            $contributor = $this->contributorRepository->find($contributorId);
            if (!$contributor) {
                throw new DomainException("Contributor $contributorId does not exist");
            }

            /**
             * @var Subscription
             */
            $subscription = $this->subscriptionRepository->findOne($extensionId, $contributorId);
            if ($subscription) {
                $subscription->confirm();
            } else {
                $extension = $this->extensionRepository->findOrCreate($extensionId);

                $subscription = new Subscription($contributor, $extension);
                $this->entityManager->persist($subscription);
            }
        }

        $this->entityManager->flush();
    }
}
