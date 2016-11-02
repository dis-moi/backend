<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Recommendation;
use AppBundle\Entity\RecommendationVisibility;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRecommendationData extends AbstractFixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $recommendation = new Recommendation();
        $recommendation->setTitle("Un site de new avec des info fiables");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("Il torche sa maman");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation', $recommendation);
    }

    public function getDependencies()
    {
        return [LoadContributorData::class];
    }
}
