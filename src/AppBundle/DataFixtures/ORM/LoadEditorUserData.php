<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadEditorUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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

        $userManager = $this->container->get('fos_user.user_manager');
        $factory = $this->container->get('security.encoder_factory');

        $user = $userManager->createUser();

        $user->setUsername('myeditoruser');
        $user->setEmail('myeditoruser@lmem.net');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_USER'));

        // the 'security.password_encoder' service requires Symfony 2.6 or higher
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('t586rt586r', $user->getSalt());

        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('editor-user', $user);
    }
}
