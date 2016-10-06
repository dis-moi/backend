<?php

namespace AppBundle\Controller;
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

class AdminApiControllerTest extends WebTestCase
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

    public function testAdminGetMatchingContextsPrivate()
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

    protected function loadFixturesFromDirectory($directory, $entityManager)
    {
        $loader = new ContainerAwareLoader($this->client->getContainer());
        $loader->loadFromDirectory($directory);

        $fixtures = $loader->getFixtures();

        $purger = new ORMPurger($entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);

        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($fixtures);
    }
}
