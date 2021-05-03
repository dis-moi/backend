<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserAdminFixtures extends Fixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

        /** @var User $user */
        $user = $userManager->createUser();

        $user->setUsername('lmem');
        $user->setEmail('infra@lmem.net');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $password = $encoder->encodePassword('LM3M!P4SSW0RD', $user->getSalt());

        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
