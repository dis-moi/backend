<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\ContributorRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadAuthorContributorData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('author-user');
        $organization = $this->getReference('organization');

        $contributor = new Contributor();
        $contributor->setUser($user);
        $contributor->setName('My Author');
        $contributor->setRole(ContributorRole::AUTHOR_ROLE());
        $contributor->setOrganization($organization);

        $manager->persist($contributor);
        $manager->flush();
        $this->addReference('author-contributor', $contributor);
    }

    public function getDependencies()
    {
        return [LoadAuthorUserData::class, LoadOrganizationData::class];
    }
}
