<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Factory\MatchingContextFactory;
use App\Entity\Contributor;
use App\Entity\Notice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NoticeMatchingContextFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Contributor $famousContributor */
        $famousContributor = $this->getReference('famous_contributor');

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('This notice has two matching contexts on same domain');
        $notice->addMatchingContext(MatchingContextFactory::create(
            $this->getReference('tests_menant_domain'),
            '.*',
            "//text()[contains(.,'Not Found')]"
        ));
        $notice->addMatchingContext(MatchingContextFactory::create(
            $this->getReference('tests_menant_domain'),
            '.*',
            "//text()[contains(.,'was not found on this server')]"
        ));
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('This notice on the same domain of others notices.');
        $notice->addMatchingContext(MatchingContextFactory::create(
            $this->getReference('tests_menant_domain'),
            '.*',
            "//text()[contains(.,'This is not an error!')]"
        ));
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('This notice appear on lbc when the word "location" is found.');
        $notice->addMatchingContext(MatchingContextFactory::create(
            $this->getReference('lbc_domain'),
            '.*',
            "//text()[contains(.,'location')]"
        ));
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage('This notice appear on lbc when the word "piscine" is found.');
        $notice->addMatchingContext(MatchingContextFactory::create(
            $this->getReference('lbc_domain'),
            '.*',
            "//text()[contains(.,'piscine')]"
        ));
        $manager->persist($notice);

        $notice = new Notice();
        $notice->setContributor($famousContributor);
        $notice->setMessage("Accès gratuit aux notes des joueurs sur d'autres sites...");
        $notice->addMatchingContext(MatchingContextFactory::create(
            $this->getReference('lequipe_domain'),
            '.*((football.*article.*notes)|(football.*notes.*match))'
        ));
        $manager->persist($notice);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ContributorFixtures::class, DomainFixtures::class];
    }
}
