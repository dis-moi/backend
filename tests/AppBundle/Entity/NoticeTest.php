<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Notice;
use AppBundle\Entity\Contributor;
use DateTimeImmutable;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Tests\FixtureAwareWebTestCase;

class NoticeTest extends FixtureAwareWebTestCase
{

    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    public function setUp()
    {
        parent::setUp();

        $this->_entityManager = static::$container->get('doctrine')->getManager();
    }

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

    public function testCreated()
    {
        /** @var Contributor $contributor */
        $contributor = $this->_getReference('contributor2');

        $notice = new Notice();
        $notice->setContributor($contributor);
        $notice->setMessage("Dumb message...");

        $created = $notice->getCreated();
        $expires = $notice->getExpires();
        $this->assertEmpty($created);
        $this->assertEmpty($expires);

        $this->_entityManager->persist($notice);
        $created = $notice->getCreated();
        $expires = $notice->getExpires();
        $this->assertInstanceOf(DateTime::class, $created);
        $this->assertInstanceOf(DateTimeImmutable::class, $expires);
        $this->assertEquals(1, $created->diff($expires, true)->y);
    }
}
