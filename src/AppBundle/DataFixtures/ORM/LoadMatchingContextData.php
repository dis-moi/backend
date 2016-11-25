<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\MatchingContext;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMatchingContextData extends  AbstractFixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://lemonde.fr");
        $matchingContext->setDescription("Un site avec des news");
        $matchingContext->setRecommendation($this->getReference('recommendation_no_criterion'));
        $manager->persist($matchingContext);
        $manager->flush();

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://site-ecologique.fr");
        $matchingContext->setDescription("Un site écologique");
        $matchingContext->setRecommendation($this->getReference('recommendation_criterion_ecology'));
        $manager->persist($matchingContext);
        $manager->flush();

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://site-ecologique-et-politique.fr");
        $matchingContext->setDescription("Un site politique et écologique");
        $matchingContext->setRecommendation($this->getReference('recommendation_criterion_ecology_and_politics'));
        $manager->persist($matchingContext);
        $manager->flush();

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://random-site.fr");
        $matchingContext->setDescription("A random site");
        $matchingContext->setRecommendation($this->getReference('recommendation_no_editor'));
        $manager->persist($matchingContext);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadRecommendationData::class];
    }
}
