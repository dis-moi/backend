<?php

namespace App\Tests\e2e;

class AuthenticationTest extends BaseApiE2eTestCase
{
    public function testLogin(): void
    {
        $client = self::createClient();

        // retrieve a token
        $response = $client->request('POST', '/v4/_jwt', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'lmem',
                'password' => 'LM3M!P4SSW0RD',
            ],
        ]);

        $json = json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }
}