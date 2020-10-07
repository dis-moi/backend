<?php

namespace Tests\e2e;

use Tests\FixtureAwareWebTestCase;

abstract class BaseApiE2eTestCase extends FixtureAwareWebTestCase
{
    /**
     * @param string $url
     *
     * @return array the response payload
     */
    protected function makeApiRequest($url)
    {
        $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $url);

        $response = $this->client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        return json_decode($response->getContent(), true);
    }
}
