<?php

namespace App\Tests\e2e;

use App\Entity\Contributor;

class PostSubscriptionsTest extends BaseApiE2eTestCase
{
    public function testPostSubscriptions()
    {
        /** @var Contributor $contributor */
        $extension1 = $this->referenceRepository->getReference('extension_1');
        $contributor = $this->referenceRepository->getReference('john_doe');

        $content = json_encode([
      $contributor->getId(),
    ]);

        $this->client->request('POST', '/api/v3/subscriptions/'.$contributor->getId(), [], [], [], $content);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }
}
