<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Helper\ArrayHelper;

class GetMatchingContextsTest extends BaseApiE2eTestCase
{
    public function testGetUnfilteredMatchingContexts(): void
    {
        $payload = $this->makeApiRequest('/api/v3/matchingcontexts');

        $this->assertMatchingContextsAllHaveValidNoticeUrls($payload);

        $mcWithXPath = ArrayHelper::find($payload, function ($matchingContext) {
            return !empty($matchingContext['xpath']);
        });
        self::assertNotNull($mcWithXPath);
    }

    public function testGetMatchingContextsForOneContributor(): void
    {
        $johnDoe = $this->referenceRepository->getReference('john_doe');

        $payload = $this->makeApiRequest('/api/v3/matchingcontexts?contributors[]='.$johnDoe->getId());

        $this->assertMatchingContextsAllHaveValidNoticeUrls($payload);
    }

    public function testGetMatchingContextsForMultipleContributors(): void
    {
        $johnDoe = $this->referenceRepository->getReference('john_doe');
        $contributor2 = $this->referenceRepository->getReference('contributor2');

        $payload = $this->makeApiRequest('/api/v3/matchingcontexts?contributors[]='.$johnDoe->getId().'&contributors[]='.$contributor2->getId());

        $this->assertMatchingContextsAllHaveValidNoticeUrls($payload);
    }

    /**
     * @param array<string, mixed> $matchingContexts
     */
    private function assertMatchingContextsAllHaveValidNoticeUrls(array $matchingContexts): void
    {
        foreach ($matchingContexts as $matchingContext) {
            self::assertRegExp('/^http.*\/api\/v3\/notices\/.*$/', $matchingContext['noticeUrl']);
        }
    }
}
