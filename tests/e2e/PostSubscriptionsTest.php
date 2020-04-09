<?php

namespace Tests\e2e;

use AppBundle\Entity\Contributor;

class PostSubscriptionsTest extends BaseApiE2eTestCase
{
    public function testPostSubscriptions()
    {
        /** @var Contributor $contributor */
        $extension1 = static::$referenceRepository->getReference('extension_1');
        $contributor = static::$referenceRepository->getReference('contributor');

        $content = json_encode([
      $contributor->getId(),
    ]);

        static::$client->request('POST', '/api/v3/subscriptions/'.$contributor->getId(), [], [], [], $content);
        $this->assertEquals(204, static::$client->getResponse()->getStatusCode());
    }
}
