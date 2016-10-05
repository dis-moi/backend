<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadContributorData;
use AppBundle\DataFixtures\ORM\LoadMatchingContextData;
use AppBundle\DataFixtures\ORM\LoadRecommendationData;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Recommendation;
use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\RecommendationVisibility;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ResourceRepository;
use AppBundle\Tests\Controller\RecommendationControllerTest;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ApiControllerTest extends WebTestCase
{
    /** @var Client  */
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $rootDir = $this->client->getKernel()->getRootDir();
        $container = $this->client->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();
        $fixtureDir = '../src/AppBundle/DataFixtures/ORM';
        $this->loadFixturesFromDirectory(sprintf("%s/%s", $rootDir, $fixtureDir), $entityManager);
    }

    public function testGetMatchingContexts()
    {
        $crawler = $this->client->request('GET', '/api/v1/matchingcontexts');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $payload = json_decode($this->client->getResponse()->getContent(), $asArray = true);
        $this->assertGreaterThanOrEqual(1, count($payload));

        $recommendationUrl = $payload[0]['recommendation_url'];
        return $recommendationUrl;

    }

    /**
     * @depends testGetMatchingContexts
     */
    public function testGetRecommendation($recommendationUrl)
    {
        echo $recommendationUrl;
        $uriPattern = "#http://[^/]*(/.*)$#";
        preg_match($uriPattern, $recommendationUrl, $matches);
        $uri = $matches[1];


        $crawler = $this->client->request('GET', $uri);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $payload = json_decode($this->client->getResponse()->getContent(), $asArray = true);
        $this->assertArrayHasKey('contributor', $payload);
        $this->assertArrayHasKey('visibility', $payload);
        $this->assertArrayHasKey('title', $payload);
        $this->assertArrayHasKey('description', $payload);
        $this->assertArrayHasKey('alternatives', $payload);
        $this->assertArrayHasKey('source', $payload);
        $this->assertArrayHasKey('criteria', $payload);
        $this->assertArrayHasKey('filters', $payload);

    }

    protected function loadFixturesFromDirectory($directory, $entityManager)
    {
        $loader = new ContainerAwareLoader($this->client->getContainer());
        $loader->loadFromDirectory($directory);
        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
