<?php

declare(strict_types=1);

namespace App\Tests\e2e;

class GetCategoriesTest extends BaseApiE2eTestCase
{
    public function testGetCategories(): void
    {
        $uri = '/api/v3/categories';
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), $uri);
    }
}
