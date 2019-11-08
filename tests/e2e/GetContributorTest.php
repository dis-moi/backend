<?php

namespace Tests\e2e;

use AppBundle\Entity\Contributor;

class GetContributorTest extends BaseApiE2eTestCase
{
    public function testGetContributor()
    {
        /** @var Contributor $contributor */
        $contributor = static::$referenceRepository->getReference('contributor');

        $payload = $this->makeApiRequest('/api/v3/contributors/'. $contributor->getId());

        $this->assertEquals('John Doe', $payload['name']);
        $this->assertEquals(2, $payload['contribution']['numberOfPublishedNotices']);
        $this->assertEquals(3, $payload['ratings']['subscribes']);
    }

    public function testFailGetDisabledNotice()
    {
        /** @var Contributor $contributor */
        $contributor = static::$referenceRepository->getReference('contributor_disabled');

        static::$client->request('GET', '/api/v3/contributors/'. $contributor->getId());
        $this->assertEquals(404, static::$client->getResponse()->getStatusCode());
    }
}
