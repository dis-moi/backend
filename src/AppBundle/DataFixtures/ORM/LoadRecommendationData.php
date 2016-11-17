<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Recommendation;
use AppBundle\Entity\RecommendationVisibility;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRecommendationData extends AbstractFixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $recommendation = new Recommendation();
        $recommendation->setTitle("Une reco sans critère");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("Il torche sa maman");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_no_criterion', $recommendation);

        $recommendation = new Recommendation();
        $recommendation->setTitle("my ecology story");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $recommendation->addCriterion($this->getReference('criterion_ecology'));
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_criterion_ecology', $recommendation);

        $recommendation = new Recommendation();
        $recommendation->setTitle("my ecology and politics story");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $recommendation->addCriterion($this->getReference('criterion_ecology'));
        $recommendation->addCriterion($this->getReference('criterion_politics'));
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_criterion_ecology_and_politics', $recommendation);
    }

    public function getDependencies()
    {
        return [LoadContributorData::class];
    }
}
