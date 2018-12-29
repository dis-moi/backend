<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contributor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadContributorData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $contributor = new Contributor();
        $contributor->setName("John Doe");
        $manager->persist($contributor);

        $this->addReference('contributor', $contributor);

        $contributor = new Contributor();
        $contributor->setName("Contributor 2");
        $manager->persist($contributor);

        $this->addReference('contributor2', $contributor);

        $manager->flush();
    }

}
