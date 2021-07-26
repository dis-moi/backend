<?php

declare(strict_types=1);

namespace App\Tests\e2e;

class AuthenticationTest extends BaseApiE2eTestCase
{
    public function testLogin(): void
    {
        $this->client->request(
            'POST',
            '/v4/_jwt',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            '{"username":"lmem", "password":"LM3M!P4SSW0RD"}'
        );
        $response = $this->client->getResponse();
        $json = json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }
}
