<?php

namespace App\DataFixtures;

use App\Entity\Extension;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExtensionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $extension1 = new Extension('EXT1');
        $extension1->confirm();
        $this->addReference('extension_1', $extension1);
        $manager->persist($extension1);

        $extension2 = new Extension('EXT2');
        $extension2->confirm();
        $this->addReference('extension_2', $extension2);
        $manager->persist($extension2);

        $extension3 = new Extension('EXT3');
        $extension3->confirm();
        $this->addReference('extension_3', $extension3);
        $manager->persist($extension3);

        $manager->flush();
    }
}
