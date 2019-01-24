<?php

namespace Tests\e2e;

use Tests\FixtureAwareWebTestCase;

abstract class BaseApiE2eTestCase extends FixtureAwareWebTestCase
{
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
