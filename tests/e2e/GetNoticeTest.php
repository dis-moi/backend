<?php

namespace App\Tests\e2e;

use App\Entity\Notice;

class GetNoticeTest extends BaseApiE2eTestCase
{
    public function testGetNotice()
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_type_ecology');

        $payload = $this->makeApiRequest('/api/v3/notices/'.$notice->getId());

        //$this->assertEquals('', $payload['contributor']['id']);
        $this->assertEquals('John Doe', $payload['contributor']['name']);
        $this->assertEquals('public', $payload['visibility']);
        $this->assertEqualHtml('<p><a href="http://link2.com?utm_medium=Dismoi_extension_navigateur" target="_blank" rel="noopener noreferrer">baz</a><br />
message<br />
<a href="http://link.com?foo=bar&utm_medium=Dismoi_extension_navigateur" target="_blank" rel="noopener noreferrer">foo</a><br />
with <a href="https://bulles.fr?utm_medium=Dismoi_extension_navigateur" target="_blank" rel="noopener noreferrer">bulles.fr</a>.</p>', $payload['message']);
        $this->assertEquals(2, $payload['ratings']['likes']);
        $this->assertEquals(0, $payload['ratings']['dislikes']);
    }

    public function testFailGetDisabledNotice()
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_disabled');

        $this->client->request('GET', '/api/v3/notices/'.$notice->getId());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testFailGetPrivateNotice()
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_private');

        $this->client->request('GET', '/api/v3/notices/'.$notice->getId());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testFailGetArchivedNotice()
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_type_ecology_archived');

        $this->client->request('GET', '/api/v3/notices/'.$notice->getId());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testFailGetUnpublishedNotice()
    {
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_expired_unpublished');

        $this->client->request('GET', '/api/v3/notices/'.$notice->getId());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
