<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Entity\Contributor;
use App\Entity\Notice;

class GetContributorTest extends BaseApiE2eTestCase
{
    public function testGetContributor(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('john_doe');
        $JohnDoeNotice = $this->referenceRepository->getReference('notice_type_ecology');
        $privateNotice = $this->referenceRepository->getReference('notice_private');
        /** @var Notice $relayedNotice */
        $relayedNotice = $this->referenceRepository->getReference('notice_liked_displayed');

        $payload = $this->makeApiRequest('/api/v3/contributors/'.$contributor->getId());

        $this->assertEquals('John Doe', $payload['name']);
        $this->assertEquals(2, $payload['contribution']['numberOfPublishedNotices']);
        $this->assertEquals(3, $payload['ratings']['subscribes']);
        $this->assertEquals(["http://localhost/api/v3/notices/{$relayedNotice->getId()}"], $payload['relayedNoticesUrls']);

        $noticesIds = array_map(function ($noticeUrl) {
            $matches = [];

            preg_match('/\\/(\\d+)$/', $noticeUrl, $matches);

            return $matches[1];
        }, $payload['noticesUrls']);

        $this->assertContains($JohnDoeNotice->getId(), $noticesIds);
        $this->assertNotContains($privateNotice->getId(), $noticesIds);
    }

    public function testFailGetDisabledNotice(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('contributor_disabled');

        $this->client->request('GET', '/api/v3/contributors/'.$contributor->getId());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
