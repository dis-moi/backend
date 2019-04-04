<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Intention;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTypeData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $type_ecology = new Intention();
        $type_ecology->setLabel('Ecology');
        $this->addReference('type_ecology', $type_ecology);
        $manager->persist($type_ecology);

        $type_politics = new Intention();
        $type_politics->setLabel('Politics');
        $manager->persist($type_politics);
        $this->addReference('type_politics', $type_politics);

        $manager->flush();
    }

}
