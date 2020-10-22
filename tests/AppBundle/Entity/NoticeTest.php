<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Notice;
use DateTime;
use DateTimeImmutable;
use Tests\FixtureAwareWebTestCase;

class NoticeTest extends FixtureAwareWebTestCase
{
    protected function _getReference(string $name)
    {
        return static::$referenceRepository->getReference($name);
    }

    public function testUpdated()
    {
        /** @var Notice $notice */
        $notice = $this->_getReference('notice_type_ecology');
        $currentDatetime = $notice->getUpdated();

        $notice->setMessage('foo');
        parent::$entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = parent::$entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertGreaterThan($currentDatetime, $persistedNotice->getUpdated());
        $currentDatetime = $persistedNotice->getUpdated();

        /** @var MatchingContext $matchingContext */
        $matchingContext = $this->_getReference('matchingContext_1');
        $matchingContext->setDescription('bar');
        parent::$entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = parent::$entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertGreaterThan($currentDatetime, $persistedNotice->getUpdated());
    }

    public function testCreated()
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

        parent::$entityManager->persist($notice);
        $created = $notice->getCreated();
        $expires = $notice->getExpires();
        $this->assertInstanceOf(DateTime::class, $created);
    }

    public function testAddRelayer()
    {
        /** @var Notice $notice */
        $notice = $this->_getReference('notice_type_ecology');
        /** @var Contributor $contributor */
        $contributor2 = $this->_getReference('contributor2');
        $notice->addRelayer($contributor2);
        parent::$entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = parent::$entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertEquals(1, $persistedNotice->getRelayersCount());
    }

    public function testRemoveRelayer()
    {
        /** @var Notice $notice */
        $notice = $this->_getReference('notice_liked_displayed');
        /** @var Contributor $johnDoe */
        $johnDoe = $this->_getReference('john_doe');
        $notice->removeRelayer($johnDoe);
        $this->assertEquals(1, $notice->getRelayersCount());
        parent::$entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = parent::$entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertEquals($notice->getRelayersCount(), $persistedNotice->getRelayersCount());
    }
}
