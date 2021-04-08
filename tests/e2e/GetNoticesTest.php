<?php

declare(strict_types=1);

namespace App\Tests\e2e;

class GetNoticesTest extends BaseApiE2eTestCase
{
    public function testGetNotice(): void
    {
        $payload = $this->makeApiRequest('/api/v3/notices');

        self::assertCount(10, $payload);

        $ids = array_map(static function ($noticeData) {
            return $noticeData['id'];
        }, $payload);

        self::assertContains($this->referenceRepository->getReference('notice_type_ecology')->getId(), $ids);
        self::assertNotContains($this->referenceRepository->getReference('notice_private')->getId(), $ids);
        self::assertNotContains($this->referenceRepository->getReference('notice_type_ecology_archived')->getId(), $ids);
        self::assertNotContains($this->referenceRepository->getReference('notice_expired_unpublished')->getId(), $ids);
        self::assertNotContains($this->referenceRepository->getReference('notice_disabled')->getId(), $ids);
    }

    public function testGetPaginatedNotices(): void
    {
        $firstPage = array_map(
            static function ($noticeData) {
                return $noticeData['id'];
            },
            $this->makeApiRequest('/api/v3/notices?limit=4')
        );

        $nextPage = array_map(
            static function ($noticeData) {
                return $noticeData['id'];
            },
            $this->makeApiRequest('/api/v3/notices?limit=4&offset=4')
        );

        self::assertCount(4, $firstPage);
        self::assertCount(4, $nextPage);

        $diff = array_diff($firstPage, $nextPage);

        self::assertCount(4, $diff);
    }
}
