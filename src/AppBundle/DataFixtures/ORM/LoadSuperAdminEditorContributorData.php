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

class LoadSuperAdminEditorContributorData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $user = $this->getReference('superadmin-user');
        $organization = $this->getReference('superadmin-organization');

        $contributor = new Contributor();
        $contributor->setUser($user);
        $contributor->setName('Admin lmem');
        $contributor->setRole(ContributorRole::EDITOR_ROLE());
        $contributor->setOrganization($organization);

        $manager->persist($contributor);
        $manager->flush();
        $this->addReference('superadmin-editor-contributor', $contributor);
    }

    public function getDependencies()
    {
        return [LoadSuperAdminUserData::class, LoadSuperAdminOrganizationData::class];
    }
}
