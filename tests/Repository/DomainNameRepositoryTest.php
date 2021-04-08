<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\DomainName;
use App\Repository\DomainNameRepository;
use App\Tests\FixtureAwareWebTestCase;

class DomainNameRepositoryTest extends FixtureAwareWebTestCase
{
    /**
     * @var DomainNameRepository
     */
    private $domainNameRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domainNameRepository = new DomainNameRepository(self::$container->get('doctrine'));
    }

    public function testFindMostSpecificFromHost(): void
    {
        /** @var DomainName $domainName */
        $domainName = $this->getDomainNameRepository()->findMostSpecificFromHost('www.pref.okinawa.jp');
        $this->assertEquals('pref.okinawa.jp', $domainName->getName());
    }

    public function getDomainNameRepository(): DomainNameRepository
    {
        return $this->domainNameRepository;
    }
}
