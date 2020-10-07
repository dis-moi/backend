<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\Notice;
use Tests\FixtureAwareWebTestCase;

class ContributorTest extends FixtureAwareWebTestCase
{
    public function testItGetTheirMostLikedNotice()
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('john_doe');
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_type_ecology');

        $mostLikedOrDisplayedNotice = $contributor->getTheirMostLikedOrDisplayedNotice();
        $this->assertEquals($notice->getId(), $mostLikedOrDisplayedNotice->getId());
    }

    public function testItGetTheirMostDisplayedNotice()
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('famous_contributor');
        /** @var Notice $notice */
        $notice = $this->referenceRepository->getReference('notice_liked_displayed');

        $mostLikedOrDisplayedNotice = $contributor->getTheirMostLikedOrDisplayedNotice();
        $this->assertEquals($notice->getId(), $mostLikedOrDisplayedNotice->getId());
    }
}
