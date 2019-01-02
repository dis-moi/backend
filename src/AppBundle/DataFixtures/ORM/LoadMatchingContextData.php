<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\MatchingContext;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMatchingContextData extends  AbstractFixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://site-ecologique.fr");
        $matchingContext->setDescription("Un site écologique");
        $matchingContext->setNotice($this->getReference('notice_type_ecology'));
        $this->setReference('matchingContext_1', $matchingContext);
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://site-ecologique-et-politique.fr");
        $matchingContext->setDescription("Un site politique et écologique");
        $matchingContext->setNotice($this->getReference('notice_type_ecology_and_politics'));
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://random-site.fr");
        $matchingContext->setDescription("A random site");
        $matchingContext->setNotice($this->getReference('notice_3'));
        $manager->persist($matchingContext);

        $matchingContext = new MatchingContext();
        $matchingContext->setUrlRegex("http://disabled.fr");
        $matchingContext->setDescription("A disabled site");
        $matchingContext->setNotice($this->getReference('notice_disabled'));
        $manager->persist($matchingContext);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadNoticeData::class];
    }
}
