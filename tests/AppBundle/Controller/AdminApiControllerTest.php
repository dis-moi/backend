<?php

namespace AppBundle\Controller;
use AppBundle\Entity\RecommendationVisibility;
use \AppBundle\TestBase;


class AdminApiControllerTest extends TestBase
{
    public function testSuperAdminSeeAllPrivateMatchingContexts()
    {
        $client = static::getLoggedInClient('lmem', 'LM3M!P4SSW0RD');

        $client->request('GET', '/api/v1/admin/matchingcontexts/private');
        $response = $client->getResponse();
        $this->assertIsSuccessfulJsonResponse($response);

        $payload = json_decode($response->getContent(), $asArray = true);
        $this->assertGreaterThanOrEqual(3, count($payload));
        $recommendationUrl = $payload[0]['recommendation_url'];
        return $recommendationUrl;
    }

    /**
     * @depends testSuperAdminSeeAllPrivateMatchingContexts
     */
    public function testSuperAdminSeeAllPrivateRecommendation($recommendationUrl)
    {
        $client = static::getLoggedInClient('lmem', 'LM3M!P4SSW0RD');

        $client->request('GET', $this->getUri($recommendationUrl));
        $response = $client->getResponse();
        $this->assertIsSuccessfulJsonResponse($response);

        $payload = json_decode($response->getContent(), $asArray = true);
        $this->assertIsValidRecommendationPayload($payload);
        $this->assertEquals($payload['visibility'], RecommendationVisibility::PRIVATE_VISIBILITY);
    }

    public function testAuthorSeeOwnPrivateMatchingContexts()
    {
        $client = static::getLoggedInClient('myauthoruser', 't586rt586r');

        $client->request('GET', '/api/v1/admin/matchingcontexts/private');
        $response = $client->getResponse();
        $this->assertIsSuccessfulJsonResponse($response);

        $payload = json_decode($response->getContent(), $asArray = true);
        $this->assertGreaterThanOrEqual(1, count($payload));

        $recommendationUrl = $payload[0]['recommendation_url'];

        return $recommendationUrl;
    }

    /**
     * @depends testAuthorSeeOwnPrivateMatchingContexts
     */
    public function testAuthorSeeOwnPrivateRecommendation($recommendationUrl)
    {
        $client = static::getLoggedInClient('myauthoruser', 't586rt586r');

        $client->request('GET', $this->getUri($recommendationUrl));
        $response = $client->getResponse();
        $this->assertIsSuccessfulJsonResponse($response);

        $payload = json_decode($response->getContent(), $asArray = true);
        $this->assertIsValidRecommendationPayload($payload);
        $this->assertEquals($payload['visibility'], RecommendationVisibility::PRIVATE_VISIBILITY);
        $this->assertEquals($payload['contributor']['name'], 'My Author');
    }

    public function testEditorSeeOrganizationPrivateMatchingContexts()
    {
        $client = static::getLoggedInClient('myeditoruser', 't586rt586r');

        $client->request('GET', '/api/v1/admin/matchingcontexts/private');
        $response = $client->getResponse();
        $this->assertIsSuccessfulJsonResponse($response);

        $payload = json_decode($response->getContent(), $asArray = true);
        $this->assertGreaterThanOrEqual(2, count($payload));

        return array_map(function($matchingContext){
            return $matchingContext['recommendation_url'];
        }, $payload);
    }

    /**
     * @depends testEditorSeeOrganizationPrivateMatchingContexts
     */
    public function testEditorSeeOrganizationPrivateRecommendation($recommendationUrls)
    {
        $client = static::getLoggedInClient('myeditoruser', 't586rt586r');

        foreach($recommendationUrls as $recommendationUrl){
            $client->request('GET', $this->getUri($recommendationUrl));
            $response = $client->getResponse();
            $this->assertIsSuccessfulJsonResponse($response);

            $payload = json_decode($response->getContent(), $asArray = true);
            $this->assertIsValidRecommendationPayload($payload);
            $this->assertEquals($payload['visibility'], RecommendationVisibility::PRIVATE_VISIBILITY);
            $this->assertEquals($payload['contributor']['organization']['name'], 'MyOrg');
        }
    }
}
