<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Extension;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadExtensionData extends AbstractFixture
{
  public function load(ObjectManager $manager)
  {
    $extension1 = new Extension('EXT1');
    $this->addReference('extension_1', $extension1);
    $manager->persist($extension1);

    $extension2 = new Extension('EXT2');
    $this->addReference('extension_2', $extension2);
    $manager->persist($extension2);

    $extension3 = new Extension('EXT3');
    $this->addReference('extension_3', $extension3);
    $manager->persist($extension3);

    $manager->flush();
  }
}
