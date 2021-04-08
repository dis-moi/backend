<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Entity\Notice;

class PostNoticeRatingTest extends BaseApiE2eTestCase
{
    public function testPostNoticeRating(): void
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_type_ecology');

        $content = json_encode([
            'ratingType' => 'like',
            'context' => [
                'datetime' => '2018-11-24T18:00:00Z',
                'url' => 'https://en.wikipedia.org/',
                'geolocation' => 'geo:37.786971,-122.399677;u=35',
            ],
            'reason' => 'foo',
        ]);

        $this->client->request('POST', '/api/v3/notices/'.$notice->getId().'/ratings', [], [], [], $content);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testPostNoticeRatingWrongType(): void
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_type_ecology');

        $content = json_encode([
            'ratingType' => 'wrongtype',
            'context' => [
                'datetime' => '2018-11-24T18:00:00Z',
                'url' => 'https://en.wikipedia.org/',
                'geolocation' => 'geo:37.786971,-122.399677;u=35',
            ],
            'reason' => 'foo',
        ]);

        $this->client->request('POST', '/api/v3/notices/'.$notice->getId().'/ratings', [], [], [], $content);
        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }
}
