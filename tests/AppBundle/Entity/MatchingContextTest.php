<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MatchingContext;
use AppBundle\Helper\Escaper;
use Tests\FixtureAwareWebTestCase;

class FakeEscaper implements Escaper
{
    public static function escape(string $input): string
    {
        return $input;
    }
}

class MatchingContextTest extends FixtureAwareWebTestCase
{
    public function testItGetFullUrlRegex(): void
    {
        $escaper = new FakeEscaper();

        /** @var MatchingContext $mc */
        $mc = $this->referenceRepository->getReference('mc_with_domain_name');
        $regex = $mc->getFullUrlRegex($escaper);
        self::assertEquals('(duckduckgo.com|www.bing.com|www.google.fr|www.qwant.com|www.yahoo.com|first.domainname.fr|second.domainname.fr)'.$mc->getUrlRegex(), $regex);

        $regex = $mc->getFullUrlRegex();
        self::assertEquals('(duckduckgo.com|www.bing.com|www.google.fr|www.qwant.com|www.yahoo.com|first.domainname.fr|second.domainname.fr)'.$mc->getUrlRegex(), $regex);

        /** @var MatchingContext $mc */
        $mc = $this->referenceRepository->getReference('mc_without_domain_name');
        $regex = $mc->getFullUrlRegex($escaper);
        self::assertEquals($mc->getUrlRegex(), $regex);
    }
}
