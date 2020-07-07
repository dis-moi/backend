<?php

namespace Tests\e2e;

use AppBundle\Entity\Notice;

class GetNoticesTest extends BaseApiE2eTestCase
{
    public function testGetNotice(): void
    {
        /** @var Notice $validNotice */
        $validNotice = static::$referenceRepository->getReference('notice_type_ecology');
        $privateNotice = static::$referenceRepository->getReference('notice_private');
        $archivedNotice = static::$referenceRepository->getReference('notice_type_ecology_archived');
        $expiredNotice = static::$referenceRepository->getReference('notice_expired_unpublished');
        $noticeByDisabledContributor = static::$referenceRepository->getReference('notice_disabled');

        $payload = $this->makeApiRequest('/api/v3/notices');

        $ids = array_map(static function ($noticeData) {
            return $noticeData['id'];
        }, $payload);

        $this->assertContains($validNotice->getId(), $ids);
        $this->assertNotContains($privateNotice->getId(), $ids);
        $this->assertNotContains($archivedNotice->getId(), $ids);
        $this->assertNotContains($expiredNotice->getId(), $ids);
        $this->assertNotContains($noticeByDisabledContributor->getId(), $ids);
    }
}
