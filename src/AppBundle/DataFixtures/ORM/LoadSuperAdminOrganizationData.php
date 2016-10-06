<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Organization;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadSuperAdminOrganizationData  extends AbstractFixture implements ContainerAwareInterface
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
        $organization = new Organization();
        $organization->setName('LMEM');
        $organization->setDescription('Le même en mieux');
        $manager->persist($organization);
        $manager->flush();

        $this->setReference('superadmin-organization', $organization);
    }
}
