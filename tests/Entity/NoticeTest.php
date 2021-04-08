<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Contributor;
use App\Entity\MatchingContext;
use App\Entity\Notice;
use App\Tests\FixtureAwareWebTestCase;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class NoticeTest extends FixtureAwareWebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_entityManager = self::$container->get('doctrine')->getManager();
    }

    protected function _getReference(string $name): object
    {
        return $this->referenceRepository->getReference($name);
    }

    public function testUpdated(): void
    {
        /** @var Notice $notice */
        $notice = $this->_getReference('notice_type_ecology');
        $currentDatetime = $notice->getUpdated();

        $notice->setMessage('foo');
        $this->_entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = $this->_entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertGreaterThan($currentDatetime, $persistedNotice->getUpdated());
        $currentDatetime = $persistedNotice->getUpdated();

        /** @var MatchingContext $matchingContext */
        $matchingContext = $this->_getReference('matchingContext_1');
        $matchingContext->setDescription('bar');
        $this->_entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = $this->_entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertGreaterThan($currentDatetime, $persistedNotice->getUpdated());
    }

    public function testCreated(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->_getReference('contributor2');

        $now = new DateTime();
        $notice = new Notice();
        $notice->setContributor($contributor);
        $notice->setMessage('Dumb message...');

        $expires = $notice->getExpires();
        $this->assertEquals(1, $now->diff($expires, true)->y);
        $this->assertInstanceOf(DateTimeImmutable::class, $expires);

        $created = $notice->getCreated();
        $this->assertEmpty($created);

        $this->_entityManager->persist($notice);
        $created = $notice->getCreated();
        $expires = $notice->getExpires();
        $this->assertInstanceOf(DateTime::class, $created);
    }

    public function testAddRelayer(): void
    {
        /** @var Notice $notice */
        $notice = $this->_getReference('notice_type_ecology');
        /** @var Contributor $contributor2 */
        $contributor2 = $this->_getReference('contributor2');
        $notice->addRelayer($contributor2);
        $this->_entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = $this->_entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertEquals(1, $persistedNotice->getRelayersCount());
    }

    public function testRemoveRelayer(): void
    {
        /** @var Notice $notice */
        $notice = $this->_getReference('notice_liked_displayed');
        /** @var Contributor $johnDoe */
        $johnDoe = $this->_getReference('john_doe');
        $notice->removeRelayer($johnDoe);
        $this->assertEquals(1, $notice->getRelayersCount());
        $this->_entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = $this->_entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertEquals($notice->getRelayersCount(), $persistedNotice->getRelayersCount());
    }
}
