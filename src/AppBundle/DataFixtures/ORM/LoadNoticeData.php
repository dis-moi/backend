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
        $notice->setMessage("message");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_ecology'));
        $notice->setSourceHref('source href 1');
        $manager->persist($notice);
        $manager->flush();
        $this->addReference('notice_type_ecology', $notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor2'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_ecology'));
        $notice->setSourceHref('source href 2');
        $manager->persist($notice);
        $manager->flush();
        $this->addReference('notice_type_ecology_and_politics', $notice);

        $notice = new Notice();
        $notice->setContributor($this->getReference('contributor'));
        $notice->setMessage("");
        $notice->setVisibility(NoticeVisibility::PUBLIC_VISIBILITY());
        $notice->setType($this->getReference('type_politics'));
        $notice->setSourceHref('source href 3');
        $manager->persist($notice);
        $manager->flush();
        $this->addReference('notice_3', $notice);
    }

    public function getDependencies()
    {
        return [
            LoadContributorData::class,
            LoadTypeData::class
        ];
    }
}
