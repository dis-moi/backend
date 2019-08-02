<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Notice;
use AppBundle\Entity\Contributor;
use AppBundle\Helper\Escaper;
use DateTimeImmutable;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Tests\FixtureAwareWebTestCase;

class FakeEscaper implements Escaper
{
    public static function escape(string $input): string
    {
        return 'totallyfake';
    }
}

class MatchingContextTest extends FixtureAwareWebTestCase
{
    public function testItGetFullUrlRegex()
    {
        $escaper = new FakeEscaper();

        /** @var MatchingContext $mc */
        $mc = static::$referenceRepository->getReference('mc_with_domain_name');
        $regex = $mc->getFullUrlRegex($escaper);
        $this->assertEquals('totallyfake' . $mc->getUrlRegex(), $regex);

        $regex = $mc->getFullUrlRegex();
        $this->assertEquals($mc->getDomainName() . $mc->getUrlRegex(), $regex);

        /** @var MatchingContext $mc */
        $mc = static::$referenceRepository->getReference('mc_without_domain_name');
        $regex = $mc->getFullUrlRegex($escaper);
        $this->assertEquals($mc->getUrlRegex(), $regex);
    }
}
