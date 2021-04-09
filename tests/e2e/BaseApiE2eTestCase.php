<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Tests\FixtureAwareWebTestCase;

abstract class BaseApiE2eTestCase extends FixtureAwareWebTestCase
{
    /**
     * @return array<mixed, mixed> the response payload
     */
    protected function makeApiRequest(string $url): array
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $url);
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        return json_decode($response->getContent(), true);
    }
}
