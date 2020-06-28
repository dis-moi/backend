<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Notice;
use AppBundle\Helper\NoticeVisibility;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNoticeData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor'));
        $notice->setMessage('<a href="http://link2.com">baz</a>
message
<a href="http://link.com?foo=bar">foo</a>
with https://bulles.fr.');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_type_ecology', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_type_ecology_and_politics', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor'));
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setExpires((new \DateTime())->modify('+3days'));
        $this->addReference('notice_3', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor3'));
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
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setExpires((new \DateTime())->modify('-3days'));
        $this->addReference('notice_expired', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage('');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setExpires((new \DateTime())->modify('-3days'));
        $notice->setUnpublishedOnExpiration(true);
        $this->addReference('notice_expired_unpublished', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor'));
        $notice->setVisibility(NoticeVisibility::PRIVATE_VISIBILITY());
        $notice->setMessage('Celle-ci n’est pas publique');
        $this->addReference('notice_private', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor_fame'));
        $notice->setMessage('This notice has been liked 3 times and displayed 4 times.');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_liked', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor_fame'));
        $notice->setMessage('This notice has been liked 3 times and displayed 5 times.');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_liked_displayed', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor_fame'));
        $notice->setMessage('This notice has been liked 2 times and displayed 6 times.');
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $this->addReference('notice_displayed', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor_fame'));
        $notice->setMessage('I am archived');
        $notice->setVisibility(NoticeVisibility::ARCHIVED_VISIBILITY());
        $this->addReference('notice_type_ecology_archived', $notice);
        $manager->persist($notice);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadContributorData::class];
    }
}
