<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Criterion;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCriterionData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $criterion_ecology = new Criterion();
        $criterion_ecology->setLabel('Ecology');
        $manager->persist($criterion_ecology);
        $manager->flush();
        $this->addReference('criterion_ecology', $criterion_ecology);

        $criterion_politics = new Criterion();
        $criterion_politics->setLabel('Politics');
        $manager->persist($criterion_politics);
        $manager->flush();
        $this->addReference('criterion_politics', $criterion_politics);
    }

}
