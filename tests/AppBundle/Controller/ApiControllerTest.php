<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadEditorData;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\FeedbackContext;
use AppBundle\Entity\Recommendation;
use AppBundle\Repository\EditorRepository;
use AppBundle\Repository\RecommendationRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Container;

class ApiControllerTest extends WebTestCase
{
    /** @var Client  */
    private static $client;
    /** @var Container */
    private static $container;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$client = static::createClient();
        static::$container = static::$client->getContainer();
        static::loadFixtures(static::$client);
    }

    public function test_GetMatchingContexts()
    {
        $crawler = static::$client->request('GET', '/api/v2/matchingcontexts');
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $payload = json_decode(static::$client->getResponse()->getContent(), $asArray = true);
        $this->assertGreaterThanOrEqual(1, count($payload));

        $recommendationUrlWithoutCriteria = $payload[0]['recommendation_url'];
        $recommendationUrlWithCriteria = $payload[1]['recommendation_url'];
        return [$recommendationUrlWithoutCriteria, $recommendationUrlWithCriteria];
    }

    public function test_GetMatchingContexts_can_be_filtered_by_criteria()
    {
        $crawler = static::$client->request('GET', '/api/v2/matchingcontexts?criteria=ecology,politics');
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $content = static::$client->getResponse()->getContent();
        $payload = json_decode($content, $asArray = true);
        $this->assertEquals(2, count($payload));
        $this->assertContains('site-ecologique.fr', $content);
        $this->assertContains('site-ecologique-et-politique.fr', $content);
    }

    public function test_GetMatchingContexts_can_be_filtered_by_editor()
    {
        /** @var EditorRepository $editorRepository */
        $editorRepository = $this->getDoctrine()->getRepository('AppBundle:Editor');
        $queChoisir = $editorRepository->findOneBy(['label' => LoadEditorData::QUE_CHOISIR]);
        $marianne = $editorRepository->findOneBy(['label' => LoadEditorData::MARIANNE]);
        $excludedEditors = sprintf('%s,%s', $queChoisir->getId(), $marianne->getId());
        $url = sprintf('/api/v2/matchingcontexts?excluded_editors=%s', $excludedEditors);

        $crawler = static::$client->request('GET', $url);
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $content = static::$client->getResponse()->getContent();
        $payload = json_decode($content, $asArray = true);
        $this->assertEquals(2, count($payload));
        $this->assertContains('site-ecologique-et-politique.fr', $content);
        $this->assertContains('random-site.fr', $content);
    }

    public function test_GetMatchingContexts_can_be_filtered_by_criteria_and_editor()
    {
        /** @var EditorRepository $editorRepository */
        $editorRepository = $this->getDoctrine()->getRepository('AppBundle:Editor');
        $queChoisir = $editorRepository->findOneBy(['label' => LoadEditorData::QUE_CHOISIR]);
        $marianne = $editorRepository->findOneBy(['label' => LoadEditorData::MARIANNE]);
        $excludedEditors = sprintf('%s,%s', $queChoisir->getId(), $marianne->getId());
        $url = sprintf('/api/v2/matchingcontexts?criteria=ecology,politics&excluded_editors=%s', $excludedEditors);

        $crawler = static::$client->request('GET', $url);
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $content = static::$client->getResponse()->getContent();
        $payload = json_decode($content, $asArray = true);
        $this->assertEquals(1, count($payload));
        $this->assertContains('site-ecologique-et-politique.fr', $content);
    }

    /**
     * @depends test_GetMatchingContexts
     */
    public function testGetRecommendation(array $recommendationUrls)
    {
        list($recommendationUrlWithoutCriteria, $recommendationUrlWithCriteria) = $recommendationUrls;
        $path = $this->extractPathFromUrl($recommendationUrlWithoutCriteria);

        $crawler = static::$client->request('GET', $path);
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $payload = json_decode(static::$client->getResponse()->getContent(), $asArray = true);
        $this->assertArrayHasKey('contributor', $payload);
        $this->assertArrayHasKey('visibility', $payload);
        $this->assertArrayHasKey('title', $payload);
        $this->assertArrayHasKey('description', $payload);
        $this->assertArrayHasKey('alternatives', $payload);
        $this->assertArrayHasKey('source', $payload);
        $this->assertArrayHasKey('resource', $payload);
        $this->assertArrayHasKey('criteria', $payload);
        $this->assertArrayHasKey('filters', $payload);

        $path = $this->extractPathFromUrl($recommendationUrlWithCriteria);

        $crawler = static::$client->request('GET', $path);
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
    }

    /**
     * @depends test_GetMatchingContexts
     */
    public function testGetCriteria()
    {
        $crawler = static::$client->request('GET', '/api/v2/criteria');
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $payload = json_decode(static::$client->getResponse()->getContent(), $asArray = true);
        $this->assertArrayHasKey('slug', $payload[0]);
        $this->assertArrayHasKey('label', $payload[0]);
    }

    /**
     * @depends test_GetMatchingContexts
     */
    public function testGetEditors()
    {
        $crawler = static::$client->request('GET', '/api/v2/editors');
        $this->assertEquals(200, static::$client->getResponse()->getStatusCode());
        $this->assertTrue(
            static::$client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
        $payload = json_decode(static::$client->getResponse()->getContent(), $asArray = true);
        $this->assertArrayHasKey('id', $payload[0]);
        $this->assertArrayHasKey('label', $payload[0]);
        $this->assertArrayHasKey('url', $payload[0]);
    }

    public function testRecommendationFeedback_approve()
    {
        $recommendationRepository = $this->getDoctrine()->getRepository('AppBundle:Recommendation');
        $randomRecommendation = $this->retrieveRandomRecommendation($recommendationRepository);

        $route = $this->getFeedbackUrl($randomRecommendation->getId());

        $upFeedback = $this->feedbackFactory('approve');
        $downFeedback = $this->feedbackFactory('unapprove');
        $upPayload = json_encode($upFeedback);
        $downPayload = json_encode($downFeedback);

        $clientUp1 = static::createClient();
        $clientUp2 = static::createClient();
        $clientDown1 = static::createClient();
        $crawlerUp1 = $clientUp1
            ->request('POST', $route,  $params = [], $files = [], $server = [], $upPayload);
        $crawlerUp2 = $clientUp2
            ->request('POST', $route,  $params = [], $files = [], $server = [], $upPayload);
        $crawlerDown1 = $clientDown1
            ->request('POST', $route,  $params = [], $files = [], $server = [], $downPayload);

        $this->assertEquals(201, $clientUp1->getResponse()->getStatusCode());
        $this->assertEquals(201, $clientUp2->getResponse()->getStatusCode());
        $this->assertEquals(201, $clientDown1->getResponse()->getStatusCode());
        /** @var Recommendation $updatedRecommendation */

        $this->getDoctrine()->getManager()->clear();
        $updatedRecommendation = $recommendationRepository->findOneBy(['id' => $randomRecommendation->getId()]);
        $feedbacks = $updatedRecommendation->getFeedbacks();
        $this->assertCount(3, $feedbacks);
        $approvedFeedbacksBalance = $updatedRecommendation->getApprovedFeedbackCount();
        $this->assertCount(1, $approvedFeedbacksBalance);

        $approvedFeedback1 = $feedbacks[0];
        $approvedFeedback2 = $feedbacks[1];
        $unapprovedFeedback1 = $feedbacks[2];
        $this->assertEquals(Feedback::APPROVE, $approvedFeedback1->getType());
        $this->assertEquals(Feedback::UNAPPROVE, $unapprovedFeedback1->getType());
        $context = $approvedFeedback1->getContext();
        $this->assertInstanceOf(FeedbackContext::class, $context);
        $this->assertEquals('2016-12-07T12:11:02', $context->getDatetime()->format('Y-m-d\TH:i:s'));
        $this->assertEquals('https://en.wikipedia.org/wiki/Abortion', $context->getUrl());

        $this->getDoctrine()->getManager()->remove($approvedFeedback1);
        $this->getDoctrine()->getManager()->remove($approvedFeedback2);
        $this->getDoctrine()->getManager()->remove($unapprovedFeedback1);
        $this->getDoctrine()->getManager()->flush();

    }

    public function testRecommendationFeedback_dismiss()
    {
        $recommendationRepository = $this->getDoctrine()->getRepository('AppBundle:Recommendation');
        $randomRecommendation = $this->retrieveRandomRecommendation($recommendationRepository);

        $route = $this->getFeedbackUrl($randomRecommendation->getId());
        $postedFeedback = $this->feedbackFactory('dismiss');
        $payload = json_encode($postedFeedback);
        $crawler = static::$client->request('POST', $route,  $params = [], $files = [], $server = [], $payload);

        $this->assertEquals(201, static::$client->getResponse()->getStatusCode());
        /** @var Recommendation $updatedRecommendation */
        $this->getDoctrine()->getManager()->clear();
        $updatedRecommendation = $recommendationRepository->findOneBy(['id' => $randomRecommendation->getId()]);
        $feedbacks = $updatedRecommendation->getFeedbacks();
        $this->assertCount(1, $feedbacks);
        $dismissed = $feedbacks[0];
        $this->assertEquals(Feedback::DISMISS, $dismissed->getType());
        $context = $dismissed->getContext();
        $this->assertInstanceOf(FeedbackContext::class, $context);
        $this->assertEquals('2016-12-07T12:11:02', $context->getDatetime()->format('Y-m-d\TH:i:s'));
        $this->assertEquals('https://en.wikipedia.org/wiki/Abortion', $context->getUrl());

        $this->getDoctrine()->getManager()->remove($dismissed);
        $this->getDoctrine()->getManager()->flush();
    }

    public function testRecommendationFeedback_report()
    {
        $recommendationRepository = $this->getDoctrine()->getRepository('AppBundle:Recommendation');
        $randomRecommendation = $this->retrieveRandomRecommendation($recommendationRepository);

        $route = $this->getFeedbackUrl($randomRecommendation->getId());
        $postedFeedback = $this->feedbackFactory('report');
        $payload = json_encode($postedFeedback);
        $crawler = static::$client->request('POST', $route,  $params = [], $files = [], $server = [], $payload);

        $this->assertEquals(201, static::$client->getResponse()->getStatusCode());
        /** @var Recommendation $updatedRecommendation */
        $this->getDoctrine()->getManager()->clear();
        $updatedRecommendation = $recommendationRepository->findOneBy(['id' => $randomRecommendation->getId()]);
        $feedbacks = $updatedRecommendation->getFeedbacks();
        $this->assertCount(1, $feedbacks);
        $reported = $feedbacks[0];
        $this->assertEquals(Feedback::REPORT, $reported->getType());
        $context = $reported->getContext();
        $this->assertInstanceOf(FeedbackContext::class, $context);
        $this->assertEquals('2016-12-07T12:11:02', $context->getDatetime()->format('Y-m-d\TH:i:s'));
        $this->assertEquals('https://en.wikipedia.org/wiki/Abortion', $context->getUrl());

        $this->getDoctrine()->getManager()->remove($reported);
        $this->getDoctrine()->getManager()->flush();
    }

    public function testRecommendationFeedback_invalidType()
    {
        $recommendationRepository = $this->getDoctrine()->getRepository('AppBundle:Recommendation');
        $randomRecommendation = $this->retrieveRandomRecommendation($recommendationRepository);

        $route = $this->getFeedbackUrl($randomRecommendation->getId());
        $postedFeedback = $this->feedbackFactory('invalid');
        $payload = json_encode($postedFeedback);
        $crawler = static::$client->request('POST', $route,  $params = [], $files = [], $server = [], $payload);

        $this->assertEquals(400, static::$client->getResponse()->getStatusCode());
    }

    public function testRecommendationFeedback_invalidStructure()
    {
        $recommendationRepository = $this->getDoctrine()->getRepository('AppBundle:Recommendation');
        $randomRecommendation = $this->retrieveRandomRecommendation($recommendationRepository);

        $route = $this->getFeedbackUrl($randomRecommendation->getId());
        $postedFeedback = ['random' => 'value'];
        $payload = json_encode($postedFeedback);
        $crawler = static::$client->request('POST', $route,  $params = [], $files = [], $server = [], $payload);

        $this->assertEquals(400, static::$client->getResponse()->getStatusCode());
    }

    public function testRecommendationFeedback_invalidDatetime()
    {
        $recommendationRepository = $this->getDoctrine()->getRepository('AppBundle:Recommendation');
        $randomRecommendation = $this->retrieveRandomRecommendation($recommendationRepository);

        $route = $this->getFeedbackUrl($randomRecommendation->getId());
        $postedFeedback = [
            'feedback' => 'dismiss',
            'context' => [
                'datetime' => 'invalid',
                'url' => 'https://en.wikipedia.org/wiki/Abortion'
            ]
        ];
        $payload = json_encode($postedFeedback);
        $crawler = static::$client->request('POST', $route,  $params = [], $files = [], $server = [], $payload);

        $this->assertEquals(400, static::$client->getResponse()->getStatusCode());
    }

    /**
     * @param RecommendationRepository $repository
     *
     * @return Recommendation
     */
    protected function retrieveRandomRecommendation(RecommendationRepository $repository)
    {
        $recommendations = $repository->findAll();
        return $recommendations[0];
    }

    /**
     * @param string $feedback
     *
     * @return array
     */
    protected function feedbackFactory($feedback)
    {
        return [
            'feedback' => $feedback,
            'context' => [
                'datetime' => '2016-12-07T12:11:02+00:00',
                'url' => 'https://en.wikipedia.org/wiki/Abortion'
            ]
        ];
    }

    protected static function loadFixtures(Client $client)
    {
        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $rootDir = $client->getKernel()->getRootDir();
        $fixtureDir = '../src/AppBundle/DataFixtures/ORM';

        $loader = new ContainerAwareLoader($client->getContainer());
        $loader->loadFromDirectory(sprintf("%s/%s", $rootDir, $fixtureDir));

        $purger = new ORMPurger();
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
        $entityManager->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));
    }

    /**
     * @param $url
     *
     * @return string
     */
    private function extractPathFromUrl($url)
    {
        $uriPattern = "#http://[^/]*(/.*)$#";
        preg_match($uriPattern, $url, $matches);
        $uri = $matches[1];
        return $uri;
    }

    /**
     * @param int $recommendationId
     *
     * @return string
     */
    private function getFeedbackUrl($recommendationId)
    {
        $route = sprintf('/api/v2/recommendations/%d/feedbacks', $recommendationId);
        return $route;
    }

    /**
     * @return Registry
     */
    private function getDoctrine()
    {
        return self::$container->get('doctrine');
    }
}
