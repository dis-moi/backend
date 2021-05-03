<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Ability\Container;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserFixtures extends Fixture implements FixtureInterface, ContainerAwareInterface
{
    use Container;

    public function load(ObjectManager $manager): void
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

        /** @var User $user */
        $user = $userManager->createUser();

        $user->setUsername('test');
        $user->setEmail('test@test.fr');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($encoder->encodePassword('test', $user->getSalt()));
        $user->addHat($this->getReference('john_doe'));
        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return [
            ContributorFixtures::class,
        ];
    }
}
