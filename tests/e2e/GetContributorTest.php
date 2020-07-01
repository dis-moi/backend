<?php

namespace Tests\e2e;

use AppBundle\Entity\Contributor;

class GetContributorTest extends BaseApiE2eTestCase
{
    public function testGetContributor()
    {
        /** @var Contributor $contributor */
        $contributor = static::$referenceRepository->getReference('contributor');
        $JohnDoeNotice = static::$referenceRepository->getReference('notice_type_ecology');
        $privateNotice = static::$referenceRepository->getReference('notice_private');

        $payload = $this->makeApiRequest('/api/v3/contributors/'.$contributor->getId());

        $this->assertEquals('John Doe', $payload['name']);
        $this->assertEquals(2, $payload['contribution']['numberOfPublishedNotices']);
        $this->assertEquals(3, $payload['ratings']['subscribes']);

        $noticesIds = array_map(function ($noticeUrl) {
            $matches = [];

            preg_match('/\\/(\\d+)$/', $noticeUrl, $matches);

            return $matches[1];
        }, $payload['noticesUrls']);

        $this->assertContains($JohnDoeNotice->getId(), $noticesIds);
        $this->assertNotContains($privateNotice->getId(), $noticesIds);
    }

    public function testFailGetDisabledNotice()
    {
        /** @var Contributor $contributor */
        $contributor = static::$referenceRepository->getReference('contributor_disabled');

        static::$client->request('GET', '/api/v3/contributors/'.$contributor->getId());
        $this->assertEquals(404, static::$client->getResponse()->getStatusCode());
    }
}
