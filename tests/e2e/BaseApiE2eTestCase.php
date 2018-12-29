<?php

namespace Tests\e2e;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class BaseApiE2eTestCase extends WebTestCase
{
    /** @var Client */
    protected static $client;
    /** @var Container */
    protected static $container;
    /** @var ReferenceRepository */
    protected static $referenceRepository;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$client = static::createClient();
        static::$container = static::$client->getContainer();
        static::loadFixtures(static::$client);
    }

    protected static function loadFixtures(Client $client)
    {
        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager();
        static::$referenceRepository = new ProxyReferenceRepository($entityManager);

        $rootDir = $client->getKernel()->getRootDir();
        $fixtureDir = '../src/AppBundle/DataFixtures/ORM';

        $loader = new ContainerAwareLoader($client->getContainer());
        $loader->loadFromDirectory(sprintf("%s/%s", $rootDir, $fixtureDir));
        static::$referenceRepository = new ProxyReferenceRepository(static::$client->getContainer()->get('doctrine')->getManager());
        $purger = new ORMPurger();
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->setReferenceRepository(static::$referenceRepository);
        $executor->execute($loader->getFixtures());
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));
    }

    /**
     * @param string $url
     * @return array the response payload
     */
    protected function makeApiRequest($url)
    {
        static::$client->request('GET', $url);
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode(), $url);

        $response = static::$client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        return json_decode($response->getContent(), true);
    }
}
