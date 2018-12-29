<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Type;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTypeData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $type_ecology = new Type();
        $type_ecology->setLabel('Ecology');
        $manager->persist($type_ecology);
        $manager->flush();
        $this->addReference('type_ecology', $type_ecology);

        $type_politics = new Type();
        $type_politics->setLabel('Politics');
        $manager->persist($type_politics);
        $manager->flush();
        $this->addReference('type_politics', $type_politics);
    }

}