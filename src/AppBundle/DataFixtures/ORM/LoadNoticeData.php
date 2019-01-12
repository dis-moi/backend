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
        $notice->setMessage("<a href=\"http://link2.com\">baz</a>
message 
<a href=\"http://link.com?foo=bar\">foo</a>");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_ecology'));
        $notice->setSourceHref('http://source-href-1.fr');
        $this->addReference('notice_type_ecology', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_ecology'));
        $notice->setSourceHref('http://source-href-2.fr');
        $this->addReference('notice_type_ecology_and_politics', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_politics'));
        $notice->setSourceHref('http://source-href-3.fr');
        $notice->setExpires((new \DateTime())->modify('+3days'));
        $this->addReference('notice_3', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor_disabled'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_politics'));
        $notice->setSourceHref('source href disabled');
        $this->addReference('notice_disabled', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_politics'));
        $notice->setSourceHref('source href expired (but not unpublished)');
        $notice->setExpires((new \DateTime())->modify('-3days'));
        $this->addReference('notice_expired', $notice);
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_politics'));
        $notice->setSourceHref('source href expired and unpublished');
        $notice->setExpires((new \DateTime())->modify('-3days'));
        $notice->setUnpublishedOnExpiration(true);
        $this->addReference('notice_expired_unpublished', $notice);
        $manager->persist($notice);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadContributorData::class,
            LoadTypeData::class
        ];
    }
}
