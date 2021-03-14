<?php

namespace App\DataFixtures;

use App\Domain\Model\Enum\CategoryName;
use App\Entity\Contributor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ContributorFixtures extends Fixture
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
        $contributor->addCategory(CategoryName::CULTURE);
        $this->addReference('contributor2', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Jane Doe');
        $contributor->setCategories([CategoryName::CULTURE, CategoryName::MILITANT]);
        $this->addReference('jane_doe', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Disabled contributor');
        $contributor->setEnabled(false);
        $this->addReference('contributor_disabled', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Famous Contributor');
        $contributor->setTitle('Famous Contributor Awesome Title');
        $contributor->addCategory(CategoryName::CONSO);
        $this->addReference('famous_contributor', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('Paul Bismuth');
        $contributor->addCategory(CategoryName::DIVERS);
        $this->addReference('contributor_lazy', $contributor);
        $manager->persist($contributor);

        $contributor = new Contributor();
        $contributor->setName('CaptainFact');
        $contributor->setIntro('Contributeur avec contributions automatisées');
        $contributor->setWebsite('captainfact.io/');
        $contributor->addCategory(CategoryName::CULTURE);
        $this->addReference('contributor_captainfact', $contributor);
        $manager->persist($contributor);

        $manager->flush();
    }
}
