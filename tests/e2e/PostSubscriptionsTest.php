<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Entity\Contributor;

class PostSubscriptionsTest extends BaseApiE2eTestCase
{
    public function testPostSubscriptions(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('john_doe');

        $content = json_encode([
            $contributor->getId(),
        ]);

        $this->client->request('POST', '/api/v3/subscriptions/'.$contributor->getId(), [], [], [], $content);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }
}
