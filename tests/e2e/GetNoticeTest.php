<?php

namespace Tests\e2e;

use AppBundle\Entity\Notice;

class GetNoticeTest extends BaseApiE2eTestCase
{
    public function testGetNotice()
    {
        /** @var Notice $notice */
        $notice = static::$referenceRepository->getReference('notice_type_ecology');

        $payload = $this->makeApiRequest('/api/v3/notices/'. $notice->getId());

        //$this->assertEquals('', $payload['contributor']['id']);
        $this->assertEquals('John Doe', $payload['contributor']['name']);
        $this->assertEquals('public', $payload['visibility']);
        $this->assertEquals('<p>message</p>', $payload['message']);
        $this->assertEquals('source href 1', $payload['sourceHref']);
        $this->assertEquals('Ecology', $payload['type']['label']);
        $this->assertEquals('ecology', $payload['type']['slug']);
        $this->assertEquals(3, $payload['ratings']['approves']);
        $this->assertEquals(2, $payload['ratings']['dislikes']);
    }
}