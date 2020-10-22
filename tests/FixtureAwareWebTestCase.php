<?php

namespace Tests;

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
    protected static $client;
    /** @var ReferenceRepository */
    protected static $referenceRepository;
    /** @var EntityManagerInterface */
    protected static $entityManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$client = static::createClient();
        static::loadFixtures(static::$client);
    }

    protected static function loadFixtures(Client $client)
    {
        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager();
        self::$entityManager = $entityManager;
        static::$referenceRepository = new ProxyReferenceRepository($entityManager);

        $rootDir = $client->getKernel()->getRootDir();
        $fixtureDir = '../src/AppBundle/DataFixtures/ORM';

        $loader = new ContainerAwareLoader($client->getContainer());
        $loader->loadFromDirectory(sprintf('%s/%s', $rootDir, $fixtureDir));
        static::$referenceRepository = new ProxyReferenceRepository(static::$client->getContainer()->get('doctrine')->getManager());
        $purger = new ORMPurger();
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->setReferenceRepository(static::$referenceRepository);
        $executor->execute($loader->getFixtures());
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));
    }

    protected static function assertEqualHtml($expected, $actual)
    {
        $from = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s'];
        $to = ['>',            '<',            '\\1',      '><'];
        self::assertEquals(
            preg_replace($from, $to, $expected),
            preg_replace($from, $to, $actual)
        );
    }
}
