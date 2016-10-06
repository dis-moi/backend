<?php

namespace AppBundle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\HttpFoundation\Response;

abstract class TestBase extends WebTestCase
{
    protected static $defaultClient;

    public static function setUpBeforeClass()
    {
        static::$defaultClient = static::createClient([], [
            'PHP_AUTH_USER' => 'lmem',
            'PHP_AUTH_PW' => 'LM3M!P4SSW0RD'
        ]);
        $rootDir = static::$defaultClient->getKernel()->getRootDir();
        $container = static::$defaultClient->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();
        $fixtureDir = '../src/AppBundle/DataFixtures/ORM';
        static::loadFixturesFromDirectory(sprintf("%s/%s", $rootDir, $fixtureDir), $entityManager);
    }

    protected function getUri($url)
    {
        $uriPattern = "#http://[^/]*(/.*)$#";
        preg_match($uriPattern, $url, $matches);
        return $matches[1];
    }

    protected static function loadFixturesFromDirectory($directory, $entityManager)
    {
        $loader = new ContainerAwareLoader(static::$defaultClient->getContainer());
        $loader->loadFromDirectory($directory);

        $fixtures = $loader->getFixtures();

        $purger = new ORMPurger($entityManager);

        $executor = new ORMExecutor($entityManager, $purger);
        //Awful but actually working ...
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $executor->execute($fixtures);
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));
    }

    protected static function getLoggedInClient($login, $password)
    {
        return static::createClient([], [
            'PHP_AUTH_USER' => $login,
            'PHP_AUTH_PW' => $password
        ]);
    }

    protected function assertIsSuccessfulJsonResponse(Response $response)
    {
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json')
        );
    }

    protected function assertIsValidRecommendationPayload($payload)
    {
        $this->assertArrayHasKey('contributor', $payload);
        $this->assertArrayHasKey('visibility', $payload);
        $this->assertArrayHasKey('title', $payload);
        $this->assertArrayHasKey('description', $payload);
        $this->assertArrayHasKey('alternatives', $payload);
        $this->assertArrayHasKey('source', $payload);
        $this->assertArrayHasKey('criteria', $payload);
        $this->assertArrayHasKey('filters', $payload);
    }
}
