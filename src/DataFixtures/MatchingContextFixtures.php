<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\MatchingContext;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MatchingContextFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://site-ecologique.fr');
        $matchingContext->setUrlRegex('http://site-ecologique.fr');
        $matchingContext->setDescription('Un site écologique');
        $matchingContext->setNotice($this->getReference('notice_type_ecology'));
        $this->setReference('matchingContext_1', $matchingContext);
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://siteecologique.fr');
        $matchingContext->setUrlRegex('http://siteecologique.fr');
        $matchingContext->setDescription('Un siteécologique');
        $matchingContext->setNotice($this->getReference('notice_liked_displayed'));
        $this->setReference('matchingContext_liked_displayed', $matchingContext);
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://site-ecologique-et-politique.fr');
        $matchingContext->setUrlRegex('http://site-ecologique-et-politique.fr');
        $matchingContext->setDescription('Un site politique et écologique');
        $matchingContext->setNotice($this->getReference('notice_type_ecology_and_politics'));
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://random-site.fr');
        $matchingContext->setUrlRegex('http://random-site.fr');
        $matchingContext->setDescription('A random site');
        $matchingContext->setNotice($this->getReference('notice_3'));
        $this->addReference('mc_without_domain_name', $matchingContext);
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://disabled.fr');
        $matchingContext->setUrlRegex('http://disabled.fr');
        $matchingContext->setDescription('A disabled site');
        $matchingContext->setNotice($this->getReference('notice_disabled'));
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://expired.fr');
        $matchingContext->setUrlRegex('http://expired.fr');
        $matchingContext->setDescription('A expired site');
        $matchingContext->setNotice($this->getReference('notice_expired'));
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('http://expired_and_unpublished.fr');
        $matchingContext->setUrlRegex('http://expired_and_unpublished.fr');
        $matchingContext->setDescription('A expired and unpublished site');
        $matchingContext->setNotice($this->getReference('notice_expired_unpublished'));
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('https://www.domainname.fr/coucou/example');
        $matchingContext->addDomainName($this->getReference('first_domain'));
        $matchingContext->addDomainName($this->getReference('second_domain'));
        $matchingContext->addDomainsSet($this->getReference('search_engines_domains_set'));
        $matchingContext->setUrlRegex('/superexample');
        $matchingContext->setDescription('With a FQDN');
        $matchingContext->setNotice($this->getReference('notice_other'));
        $this->addReference('mc_with_domain_name', $matchingContext);
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('https://www.dismoi.io/aide/');
        $matchingContext->addDomainName($this->getReference('dismoi_domain'));
        $matchingContext->setUrlRegex('/aide');
        $matchingContext->setDescription("Page d'aide de Dismoi");
        $matchingContext->setXpath("//text()[contains(.,'Aide')]");
        $matchingContext->setNotice($this->getReference('notice_xpath_match'));
        $this->addReference('mc_matching_xpath', $matchingContext);
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setExampleUrl('https://www.dismoi.io/aide/');
        $matchingContext->addDomainName($this->getReference('dismoi_domain'));
        $matchingContext->setUrlRegex('/aide');
        $matchingContext->setDescription('No future');
        $matchingContext->setXpath("//text()[contains(.,'No future')]");
        $matchingContext->setNotice($this->getReference('notice_xpath_no_match'));
        $this->addReference('mc_non_matching_xpath', $matchingContext);
        $manager->persist($matchingContext);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [NoticeFixtures::class, DomainFixtures::class];
    }
}
