<?php

namespace Tests\e2e;

class GetMatchingContextsTest extends BaseApiE2eTestCase
{
    public function getMatchingContextsData(): array
    {
        return [
            [null, 6, ['http://site-ecologique.fr', 'http://random-site.fr', 'http://site-ecologique-et-politique.fr', 'http://expired.fr', "(duckduckgo\.com|www\.bing\.com|www\.google\.fr|www\.qwant\.com|www\.yahoo\.com|first\.domainname\.fr|second\.domainname\.fr)/superexample", 'http://siteecologique.fr']],
            [['john_doe', 'contributor2'], 5, ['http://site-ecologique.fr', 'http://site-ecologique-et-politique.fr', 'http://random-site.fr', 'http://expired.fr', 'http://siteecologique.fr']],
            [['john_doe'], 3, ['http://site-ecologique.fr', 'http://random-site.fr', 'http://siteecologique.fr']],
            [['contributor2'], 2, ['http://site-ecologique-et-politique.fr', 'http://expired.fr']],
        ];
    }

    /**
     * @dataProvider getMatchingContextsData
     */
    public function testGetMatchingContexts(?array $contributors, int $count, array $urlRegexes): void
    {
        $url = '/api/v3/matchingcontexts';
        if ($contributors) {
            $url .= '?'.implode('&', array_map(function ($contributorReference) {
                return 'contributors[]='.$this->referenceRepository->getReference($contributorReference)->getId();
            }, $contributors));
        }
        $payload = $this->makeApiRequest($url);

        self::assertCount($count, $payload);

        foreach ($payload as $matchingContext) {
            self::assertRegExp('/^http.*\/api\/v3\/notices\/.*$/', $matchingContext['noticeUrl']);
        }

        self::assertEqualsCanonicalizing($urlRegexes, array_map(static function ($matchingContext) {
            return $matchingContext['urlRegex'];
        }, $payload));
    }
}
