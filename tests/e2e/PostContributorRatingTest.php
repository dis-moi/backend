<?php

namespace Tests\e2e;

use AppBundle\Entity\Contributor;

class PostContributorRatingTest extends BaseApiE2eTestCase
{
    public function testPostContributorRating()
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('john_doe');

        $content = json_encode([
            'ratingType' => 'subscribe',
        ]);

        $this->client->request('POST', '/api/v3/contributors/'.$contributor->getId().'/ratings', [], [], [], $content);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }
}
