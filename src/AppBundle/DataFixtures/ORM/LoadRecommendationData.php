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
        $recommendation->setTitle("Une reco sans critère de que choisir");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("Il torche sa maman");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $recommendation->setResource($this->getReference('resource_link_quechoisir'));
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_no_criterion', $recommendation);

        $recommendation = new Recommendation();
        $recommendation->setTitle("my ecology story from marianne");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $recommendation->setResource($this->getReference('resource_link_marianne'));
        $recommendation->addCriterion($this->getReference('criterion_ecology'));
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_criterion_ecology', $recommendation);

        $recommendation = new Recommendation();
        $recommendation->setTitle("my ecology and politics story from huffington");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $recommendation->setResource($this->getReference('resource_link_huffington'));
        $recommendation->addCriterion($this->getReference('criterion_ecology'));
        $recommendation->addCriterion($this->getReference('criterion_politics'));
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_criterion_ecology_and_politics', $recommendation);

        $recommendation = new Recommendation();
        $recommendation->setTitle("my story without editor");
        $recommendation->setContributor($this->getReference('contributor'));
        $recommendation->setDescription("");
        $recommendation->setVisibility(RecommendationVisibility::PUBLIC_VISIBILITY());
        $recommendation->setResource($this->getReference('resource_with_no_editor'));
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('recommendation_no_editor', $recommendation);
    }

    public function getDependencies()
    {
        return [LoadContributorData::class];
    }
}
