<?php

namespace App\Tests\Repository;

use App\Entity\DomainName;
use App\Repository\DomainNameRepository;
use App\Tests\FixtureAwareWebTestCase;

class DomainNameRepositoryTest extends FixtureAwareWebTestCase
{
    private $domainNameRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domainNameRepository = new DomainNameRepository(self::$container->get('doctrine')->getManager());
    }

    public function testFindMostSpecificFromHost()
    {
        /* @var DomainName $domainName */
        $domainName = $this->getDomainNameRepository()->findMostSpecificFromHost('www.pref.okinawa.jp');
        $this->assertEquals('pref.okinawa.jp', $domainName->getName());
    }

    public function getDomainNameRepository(): DomainNameRepository
    {
        return $this->domainNameRepository;
    }
}
