<?php

namespace Tests\e2e;

class GetMatchingContextsTest extends BaseApiE2eTestCase
{
    public function getMatchingContextsData()
    {
        return [
            [null, 6, ["http://site-ecologique.fr", "http://random-site.fr", "http://site-ecologique-et-politique.fr", "http://expired.fr", "(www\.google\.fr|www\.bing\.com|duckduckgo\.com|www\.qwant\.com|www\.yahoo\.com|first\.domainname\.fr|second\.domainname\.fr)/superexample", "http://siteecologique.fr"]],
            [['contributor', 'contributor2'], 4, ["http://site-ecologique.fr", "http://site-ecologique-et-politique.fr", "http://random-site.fr", "http://expired.fr"]],
            [['contributor'], 2, ["http://site-ecologique.fr", "http://random-site.fr"]],
            [['contributor2'], 2, ["http://site-ecologique-et-politique.fr", "http://expired.fr"]]
        ];
    }

    /**
     * @dataProvider getMatchingContextsData
     */
    public function testGetMatchingContexts(?array $contributors, int $count, array $urlRegexes)
    {
        $url = '/api/v3/matchingcontexts';
        if($contributors) {
            $url .= '?'. implode('&', array_map(function($contributorReference) {
                return 'contributors[]='. static::$referenceRepository->getReference($contributorReference)->getId();
                }, $contributors));
        }
        $payload = $this->makeApiRequest($url);

        $this->assertEquals($count, count($payload));

        foreach ($payload as $matchingContext) {
            $this->assertEquals(1, preg_match('/^http.*\/api\/v3\/notices\/.*$/', $matchingContext["noticeUrl"]));
        }

        $this->assertEquals($urlRegexes, array_map(function ($matchingContext) {
            return $matchingContext['urlRegex'];
        }, $payload));

    }
}
