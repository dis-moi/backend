<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Contributor;
use App\Entity\Notice;
use App\Helper\NoticeVisibility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NoticeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $johnDoe = $this->getReference('john_doe');
        $janeDoe = $this->getReference('jane_doe');
        /** @var Contributor $famousContributor */
        $famousContributor = $this->getReference('famous_contributor');
        $contributor2 = $this->getReference('contributor2');

        $notice = new Notice();
        $notice->setContributor($johnDoe);
        $notice->setMessage('<a href="http://link2.com">baz</a>
message
<a href="http://link.com?foo=bar">foo</a>
with https://bulles.fr.');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_type_ecology', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($contributor2);
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_type_ecology_and_politics', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($johnDoe);
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setExpires((new \DateTime())->modify('+3days'));
        $this->addReference('notice_3', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($janeDoe);
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_other', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor_disabled'));
        $notice->setMessage('Notice by a disabled contributor');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_disabled', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($contributor2);
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setExpires((new \DateTime())->modify('-3days'));
        $this->addReference('notice_expired', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($contributor2);
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setExpires((new \DateTime())->modify('-3days'));
        $notice->setUnpublishedOnExpiration(true);
        $this->addReference('notice_expired_unpublished', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($johnDoe);
        $notice->setVisibility(NoticeVisibility::PRIVATE_VISIBILITY());
        $notice->setMessage('Celle-ci n’est pas publique');
        $this->addReference('notice_private', $notice);
        $manager->persist($notice);

        $noticeLiked = new Notice();
        $noticeLiked->setContributor($famousContributor);
        $noticeLiked->setMessage('This notice has been liked 3 times and displayed 4 times.');
        $noticeLiked->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_liked', $noticeLiked);
        $manager->persist($noticeLiked);

        $noticeLikedDisplayed = new Notice();
        $noticeLikedDisplayed->setContributor($famousContributor);
        $noticeLikedDisplayed->addRelayer($johnDoe);
        $noticeLikedDisplayed->addRelayer($janeDoe);
        $noticeLikedDisplayed->setMessage('This notice has been liked 3 times and displayed 5 times.');
        $noticeLikedDisplayed->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_liked_displayed', $noticeLikedDisplayed);
        $manager->persist($noticeLikedDisplayed);

        $noticeDisplayed = new Notice();
        $noticeDisplayed->setContributor($famousContributor);
        $noticeDisplayed->setMessage('This notice has been liked 2 times and displayed 6 times.');
        $noticeDisplayed->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_displayed', $noticeDisplayed);
        $manager->persist($noticeDisplayed);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('I am archived');
        $notice->setVisibility(NoticeVisibility::ARCHIVED_VISIBILITY());
        $this->addReference('notice_type_ecology_archived', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('This notice should be visible on https://www.dismoi.io/aide/');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_xpath_match', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('This notice should __not__ be visible on https://www.dismoi.io/aide/');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_xpath_no_match', $notice);
        $manager->persist($notice);

        $manager->flush();

        $famousContributor->setPinnedNotices(new ArrayCollection([
            $noticeLikedDisplayed->setPinnedSort(0),
            $noticeLiked->setPinnedSort(1),
            $noticeDisplayed->setPinnedSort(2),
        ]));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ContributorFixtures::class];
    }
}
