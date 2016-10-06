<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\ContributorRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadContributorData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        $user = $this->getReference('admin-user');
        $organization = $this->getReference('admin-organization');

        $contributor = new Contributor();
        $contributor->setUser($user);
        $contributor->setName('Admin lmem');
        $contributor->setRole(ContributorRole::EDITOR_ROLE());
        $contributor->setOrganization($organization);

        $manager->persist($contributor);
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}