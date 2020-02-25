<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\DomainName;
use AppBundle\Entity\DomainsSet;
use AppBundle\Entity\MatchingContext;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDomainData extends AbstractFixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $domainName1 = new DomainName("first.domainname.fr");
        $manager->persist($domainName1);
        $this->addReference('first_domain', $domainName1);

        $domainName2 = new DomainName("second.domainname.fr");
        $manager->persist($domainName2);
        $this->addReference('second_domain', $domainName2);

        $google = new DomainName('www.google.fr');
        $manager->persist($google);
        $this->addReference('google_domain', $google);

        $bing = new DomainName('www.bing.com');
        $manager->persist($bing);
        $this->addReference('bing_domain', $bing);

        $duckduckgo = new DomainName('duckduckgo.com');
        $manager->persist($duckduckgo);
        $this->addReference('duckduckgo_domain', $duckduckgo);

        $qwant = new DomainName('www.qwant.com');
        $manager->persist($qwant);
        $this->addReference('qwant_domain', $qwant);

        $yahoo = new DomainName('www.yahoo.com');
        $manager->persist($yahoo);
        $this->addReference('yahoo_domain', $yahoo);

        $searchEnginesSet = new DomainsSet('Search Engines');
        $searchEnginesSet->addDomain($google);
        $searchEnginesSet->addDomain($bing);
        $searchEnginesSet->addDomain($duckduckgo);
        $searchEnginesSet->addDomain($qwant);
        $searchEnginesSet->addDomain($yahoo);
        $manager->persist($searchEnginesSet);
        $this->addReference('search_engines_domains_set', $searchEnginesSet);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadNoticeData::class];
    }
}
