<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Embeddable\Context;
use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRatingData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var Notice $notice */
        $notice = $this->getReference('notice_type_ecology');

        foreach ([
            Rating::LIKE, Rating::LIKE, Rating::LIKE, Rating::UNLIKE, // 3 - 1 = 2
            Rating::DISLIKE, Rating::UNDISLIKE, Rating::UNDISLIKE, // 1 - 2 = 0
            Rating::DISMISS, Rating::DISMISS, Rating::UNDISMISS,   // 2 - 1 = 1
            Rating::BADGE, Rating::DISPLAY, Rating::UNFOLD,
            Rating::OUTBOUND_CLICK,
        ] as $type) {
            $rating = new Rating($notice, $type, new Context(new \DateTime('-1 month'), 'url', 'geoloc'), 'reason');
            $manager->persist($rating);
        }

        $notice = $this->getReference('notice_liked');
        foreach ([
                     Rating::LIKE, Rating::LIKE, Rating::LIKE,
                     Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY,
                 ] as $type) {
            $rating = new Rating($notice, $type, new Context(new \DateTime('-1 month'), 'url', 'geoloc'), 'reason');
            $manager->persist($rating);
        }
        $notice = $this->getReference('notice_displayed');
        foreach ([
                     Rating::LIKE, Rating::LIKE,
                     Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY,
                 ] as $type) {
            $rating = new Rating($notice, $type, new Context(new \DateTime('-1 month'), 'url', 'geoloc'), 'reason');
            $manager->persist($rating);
        }
        $notice = $this->getReference('notice_liked_displayed');
        foreach ([
                     Rating::LIKE, Rating::LIKE, Rating::LIKE,
                     Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY, Rating::DISPLAY,
                 ] as $type) {
            $rating = new Rating($notice, $type, new Context(new \DateTime('-1 month'), 'url', 'geoloc'), 'reason');
            $manager->persist($rating);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadNoticeData::class,
        ];
    }
}
