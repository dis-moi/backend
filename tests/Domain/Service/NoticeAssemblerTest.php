<?php

declare(strict_types=1);

namespace Tests\Domain\Service;

use App\Domain\Service\NoticeAssembler;
use App\DTO\Contribution;
use App\Entity\Contributor;
use App\Entity\DomainName;
use App\Entity\MatchingContext;
use App\Entity\Notice;
use App\Repository\ContributorRepository;
use App\Repository\DomainNameRepository;
use PHPUnit\Framework\TestCase;

class NoticeAssemblerTest extends TestCase
{
    /**
     * @var NoticeAssembler
     */
    private $noticeAssembler;

    protected function setUp(): void
    {
        parent::setUp();
        $contributorRepository = $this->getMockBuilder(ContributorRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contributorRepository
            ->expects($this->atLeast(1))->method('findOneBy')
            ->willReturn(new Contributor());

        $domainNameRepository = $this->getMockBuilder(DomainNameRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $domainNameRepository
            ->expects($this->atLeast(1))
            ->method('findMostSpecificFromHost')
            ->willReturn(null);

        $this->noticeAssembler = new NoticeAssembler($contributorRepository, $domainNameRepository);
    }

    public function testInstancesTypes(): void
    {
        $contribution = new Contribution(
            'https://www.dismoi.io/confidentialite',
            'Johan Dufour',
            'johan@dismoi.io',
            'I would prefer seeing a markdown explaining the technical perspectives on the matter.'
        );

        $this->assertInstanceOf(Notice::class, $this->noticeAssembler->assembleNotice($contribution));
        $this->assertInstanceOf(MatchingContext::class, $this->noticeAssembler->assembleMatchingContext($contribution));
        $this->assertInstanceOf(Contributor::class, $this->noticeAssembler->assembleContributor($contribution));
        $this->assertInstanceOf(DomainName::class, $this->noticeAssembler->assembleDomainName($contribution));

        $this->assertInstanceOf(Notice::class, $this->noticeAssembler->assemble($contribution));
    }
}
