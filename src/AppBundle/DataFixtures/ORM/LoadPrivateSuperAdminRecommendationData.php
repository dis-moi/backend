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

class LoadPrivateSuperAdminRecommendationData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $recommendation = new Recommendation();
        $recommendation->setTitle("My superadmin private reco");
        $recommendation->setContributor($this->getReference('superadmin-editor-contributor'));
        $recommendation->setDescription("Il torche sa maman");
        $recommendation->setVisibility(RecommendationVisibility::PRIVATE_VISIBILITY());
        $manager->persist($recommendation);
        $manager->flush();
        $this->addReference('private-superadmin-recommendation', $recommendation);
    }
    public function getDependencies()
    {
        return [LoadSuperAdminEditorContributorData::class];
    }
}
