<?php

namespace Tests\e2e;

use AppBundle\Entity\Contributor;

class PostContributorRatingTest extends BaseApiE2eTestCase
{
    public function testPostContributorRating()
    {
        /** @var Contributor $contributor */
        $contributor = static::$referenceRepository->getReference('contributor');

        $content = json_encode([
            'ratingType' => 'subscribe'
        ]);

        static::$client->request('POST', '/api/v3/contributors/'. $contributor->getId() .'/ratings', [], [], [], $content);
        $this->assertEquals(204, static::$client->getResponse()->getStatusCode());
    }
}
