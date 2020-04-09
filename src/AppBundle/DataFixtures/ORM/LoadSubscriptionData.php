<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Subscription;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSubscriptionData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $subscription1 = new Subscription($this->getReference('contributor'), $this->getReference('extension_1'));
        $manager->persist($subscription1);
        $subscription2 = new Subscription($this->getReference('contributor'), $this->getReference('extension_2'));
        $manager->persist($subscription2);
        $subscription3 = new Subscription($this->getReference('contributor'), $this->getReference('extension_3'));
        $manager->persist($subscription3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadContributorData::class, LoadExtensionData::class];
    }
}
