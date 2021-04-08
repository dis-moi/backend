<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FixtureAwareWebTestCase extends WebTestCase
{
    /** @var Client */
    protected $client;
    /** @var ReferenceRepository */
    protected $referenceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->client = self::createClient();
        $this->loadFixtures();
    }

    protected function loadFixtures(): void
    {
        $doctrine = self::$container->get('doctrine');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager();
        $this->referenceRepository = new ProxyReferenceRepository($entityManager);

        $rootDir = self::$kernel->getRootDir();
        $fixtureDir = '../src/DataFixtures';

        $loader = new ContainerAwareLoader(self::$kernel->getContainer());
        $loader->loadFromDirectory(sprintf('%s/%s', $rootDir, $fixtureDir));
        $this->referenceRepository = new ProxyReferenceRepository(self::$kernel->getContainer()->get('doctrine')->getManager());
        $purger = new ORMPurger();
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->setReferenceRepository($this->referenceRepository);
        $executor->execute($loader->getFixtures());
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));
    }

    protected static function assertEqualHtml(string $expected, string $actual): void
    {
        $from = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s'];
        $to = ['>',            '<',            '\\1',      '><'];
        self::assertEquals(
            preg_replace($from, $to, $expected),
            preg_replace($from, $to, $actual)
        );
    }
}
