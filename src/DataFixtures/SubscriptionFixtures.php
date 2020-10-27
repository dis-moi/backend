<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $subscription1 = new Subscription($this->getReference('john_doe'), $this->getReference('extension_1'));
        $manager->persist($subscription1);
        $subscription2 = new Subscription($this->getReference('john_doe'), $this->getReference('extension_2'));
        $manager->persist($subscription2);
        $subscription3 = new Subscription($this->getReference('john_doe'), $this->getReference('extension_3'));
        $manager->persist($subscription3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ContributorFixtures::class, ExtensionFixtures::class];
    }
}
