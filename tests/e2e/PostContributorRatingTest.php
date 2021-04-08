<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Entity\Contributor;

class PostContributorRatingTest extends BaseApiE2eTestCase
{
    public function testPostContributorRating(): void
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
