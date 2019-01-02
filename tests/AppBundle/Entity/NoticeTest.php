<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Notice;
use Doctrine\ORM\EntityManagerInterface;
use Tests\FixtureAwareWebTestCase;

class NoticeTest extends FixtureAwareWebTestCase
{
    public function testUpdated()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::$container->get('doctrine')->getManager();

        /** @var Notice $notice */
        $notice = static::$referenceRepository->getReference('notice_type_ecology');
        $currentDatetime = $notice->getUpdated();

        $notice->setMessage('foo');
        $entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = $entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertGreaterThan($currentDatetime, $persistedNotice->getUpdated());
        $currentDatetime = $persistedNotice->getUpdated();

        /** @var MatchingContext $matchingContext */
        $matchingContext = static::$referenceRepository->getReference('matchingContext_1');
        $matchingContext->setDescription('bar');
        $entityManager->flush();

        /** @var Notice $persistedNotice */
        $persistedNotice = $entityManager->getRepository(Notice::class)->find($notice->getId());
        $this->assertGreaterThan($currentDatetime, $persistedNotice->getUpdated());
    }
}
