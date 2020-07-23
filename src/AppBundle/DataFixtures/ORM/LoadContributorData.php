<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contributor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContributorData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $contributor = new Contributor();
        $contributor->setName('John Doe');
        $contributor->setIntro('I’m all out of bubble gum (https://www.youtube.com/watch?reload=9&v=yMN0yvot6dM)');
        $contributor->setWebsite('johndoe.com');
        $contributor->setEmail('john@doe.com');
        $contributor->setImage('photo-fake.jpg');
        $this->addReference('john_doe', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Contributor 2');
        $this->addReference('contributor2', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Jane Doe');
        $this->addReference('jane_doe', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Disabled contributor');
        $contributor->setEnabled(false);
        $this->addReference('contributor_disabled', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Famous Contributor');
        $this->addReference('famous_contributor', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Paul Bismuth');
        $this->addReference('contributor_lazy', $contributor);
        $manager->persist($contributor);

        $manager->flush();
    }
}
