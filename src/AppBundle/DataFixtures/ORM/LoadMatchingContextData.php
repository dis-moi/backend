<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\MatchingContext;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMatchingContextData extends  AbstractFixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://lemonde.fr");
        $matchingContext->setDescription("Un site avec des news");
        $matchingContext->setRecommendation($this->getReference('recommendation'));
        $manager->persist($matchingContext);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadRecommendationData::class];
    }
}
