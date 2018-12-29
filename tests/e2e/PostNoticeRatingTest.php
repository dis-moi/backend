<?php

namespace Tests\e2e;

use AppBundle\Entity\Notice;

class PostNoticeRatingTest extends BaseApiE2eTestCase
{
    public function testPostNoticeRating()
    {
        /** @var Notice $notice */
        $notice = static::$referenceRepository->getReference('notice_type_ecology');

        $content = json_encode([
            'ratingType' => 'approve',
            'context' => [
                'datetime' => '2018-11-24T18:00:00Z',
                'url' => 'https://en.wikipedia.org/',
                'geolocation' => 'geo:37.786971,-122.399677;u=35'
            ],
            'reason' => 'foo'
        ]);

        static::$client->request('POST', '/api/v3/notices/'. $notice->getId() .'/ratings', [], [], [], $content);
        $this->assertEquals(204, static::$client->getResponse()->getStatusCode());
    }
}
