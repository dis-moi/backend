<?php

namespace Tests\e2e;

use AppBundle\Entity\Notice;

class GetNoticeTest extends BaseApiE2eTestCase
{
    public function testGetNotice()
    {
        /** @var Notice $notice */
        $notice = static::$referenceRepository->getReference('notice_type_ecology');

        $payload = $this->makeApiRequest('/api/v3/notices/'.$notice->getId());

        //$this->assertEquals('', $payload['contributor']['id']);
        $this->assertEquals('John Doe', $payload['contributor']['name']);
        $this->assertEquals('public', $payload['visibility']);
        $this->assertEquals('<p><a target="_blank" rel="noopener noreferrer" href="http://link2.com">baz</a></p>
<p>message</p>
<p><a target="_blank" rel="noopener noreferrer" href="http://link.com?foo=bar">foo</a></p>
<p>with <a target="_blank" rel="noopener noreferrer" href="https://bulles.fr">bulles.fr</a>.</p>', $payload['message']);
        $this->assertEquals('alternative', $payload['intention']);
        $this->assertEquals(2, $payload['ratings']['likes']);
        $this->assertEquals(0, $payload['ratings']['dislikes']);
    }

    public function testFailGetDisabledNotice()
    {
        /** @var Notice $notice */
        $notice = static::$referenceRepository->getReference('notice_disabled');

        static::$client->request('GET', '/api/v3/notices/'.$notice->getId());
        $this->assertEquals(404, static::$client->getResponse()->getStatusCode());
    }
}
