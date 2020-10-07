<?php

namespace Tests\e2e;

use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Notice;

class GetContributorsTest extends BaseApiE2eTestCase
{
    public function testGetContributors()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $this->assertEquals(4, count($payload));
        $this->assertEquals('John Doe', $payload[0]['name']);
        $this->assertEqualHtml(
            '<p>I’m all out of bubble gum (<a href="https://www.youtube.com/watch?reload=9&v=yMN0yvot6dM&utm_medium=Dismoi_extension_navigateur" target="_blank" rel="noopener noreferrer">www.youtube.com/watch</a>)</p>',
            $payload[0]['intro']
        );
        $this->assertStringContainsString('photo-fake.jpg', $payload[0]['avatar']['small']['url']);
        $this->assertStringContainsString('photo-fake.jpg', $payload[0]['avatar']['normal']['url']);
        $this->assertStringContainsString('photo-fake.jpg', $payload[0]['avatar']['large']['url']);
        $this->assertEquals('Contributor 2', $payload[1]['name']);
    }

    /**
     * @deprecated
     */
    public function testGetContributorsCount()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $this->assertEquals(2, $payload[0]['contributions']); // 2 public + 1 private
        $this->assertEquals(3, $payload[1]['contributions']); // 3 public
    }

    public function testGetContributorsContribCount()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $this->assertEquals(2, $payload[0]['contribution']['numberOfPublishedNotices']); // 2 public + 1 private
        $this->assertEquals(3, $payload[1]['contribution']['numberOfPublishedNotices']); // 3 public
    }

    public function testGetContributorsRatings()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $this->assertEquals(3, $payload[0]['ratings']['subscribes']);
    }

    public function testGetContributorsContribExample()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        /** @var MatchingContext $mc */
        $mc = $this->referenceRepository->getReference('matchingContext_1');
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_type_ecology');

        $this->assertEquals($mc->getExampleUrl(), $payload[0]['contribution']['example']['matchingUrl']);
        $this->assertEquals($notice->getId(), $payload[0]['contribution']['example']['noticeId']);
        $this->assertStringEndsWith('/api/v3/notices/'.$notice->getId(), $payload[0]['contribution']['example']['noticeUrl']);
    }
}
