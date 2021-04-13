<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Entity\MatchingContext;
use App\Entity\Notice;
use App\Helper\CollectionHelper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GetContributorsTest.
 */
class GetContributorsTest extends BaseApiE2eTestCase
{
    public function testGetContributors(): void
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        self::assertCount(4, $payload);
        self::assertEquals('John Doe', $payload[0]['name']);
        self::assertEqualHtml(
            '<p>I’m all out of bubble gum (<a href="https://www.youtube.com/watch?reload=9&v=yMN0yvot6dM&utm_medium=Dismoi_extension_navigateur" target="_blank" rel="noopener noreferrer">www.youtube.com/watch</a>)</p>',
            $payload[0]['intro']
        );
        self::assertStringContainsString('photo-fake.jpg', $payload[0]['avatar']['small']['url']);
        self::assertStringContainsString('photo-fake.jpg', $payload[0]['avatar']['normal']['url']);
        self::assertStringContainsString('photo-fake.jpg', $payload[0]['avatar']['large']['url']);
        self::assertEquals('Contributor 2', $payload[1]['name']);
    }

    /**
     * @deprecated
     */
    public function testGetContributorsCount(): void
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        self::assertEquals(2, $payload[0]['contributions']); // 2 public + 1 private
        self::assertEquals(3, $payload[1]['contributions']); // 3 public
    }

    public function testGetContributorsContribCount(): void
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        self::assertEquals(2, $payload[0]['contribution']['numberOfPublishedNotices']); // 2 public + 1 private
        self::assertEquals(3, $payload[1]['contribution']['numberOfPublishedNotices']); // 3 public
    }

    public function testGetContributorsRatings(): void
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        self::assertEquals(3, $payload[0]['ratings']['subscribes']);
    }

    public function testGetContributorsPinnedNotices(): void
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $fetchedContributors = $payload;

        $fetchedFamousContributor = CollectionHelper::find(new ArrayCollection($fetchedContributors), static function ($contributor) { return 'Famous Contributor' === $contributor['name']; });
        $fetchedFirstPinnedNotice = $fetchedFamousContributor['contribution']['pinnedNotices'][0];

        /** @var MatchingContext $mc */
        $mc = $this->referenceRepository->getReference('matchingContext_liked_displayed');
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_liked_displayed');

        self::assertEquals($mc->getExampleUrl(), $fetchedFirstPinnedNotice['exampleMatchingUrl']);
        self::assertEquals($notice->getId(), $fetchedFirstPinnedNotice['noticeId']);
        self::assertStringEndsWith('/api/v3/notices/'.$notice->getId(), $fetchedFirstPinnedNotice['noticeUrl']);
    }
}
