<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Source;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSourceData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $source_link_quechoisir = new Source();
        $source_link_quechoisir->setUrl('http://source-href-1.fr');
        $source_link_quechoisir->setLabel('Que Choisir: Iphone is the best phone');
        $manager->persist($source_link_quechoisir);
        $manager->flush();
        $this->addReference('source_link_quechoisir', $source_link_quechoisir);

        $source_link_marianne = new Source();
        $source_link_marianne->setUrl('marianne.fr/great-article');
        $source_link_marianne->setLabel('Marianne: Super article');
        $manager->persist($source_link_marianne);
        $manager->flush();
        $this->addReference('source_link_marianne', $source_link_marianne);

        $source_link_huffington = new Source();
        $source_link_huffington->setUrl('huffington.fr/trump-will-save-us-all');
        $source_link_huffington->setLabel('Huffington Post: How Trump will make america great again');
        $manager->persist($source_link_huffington);
        $manager->flush();
        $this->addReference('source_link_huffington', $source_link_huffington);

        $source_link_no_editor = new Source();
        $source_link_no_editor->setUrl('http://disabled.com');
        $source_link_no_editor->setLabel('A source with notice disabled');
        $manager->persist($source_link_no_editor);
        $manager->flush();
        $this->addReference('source_disabled', $source_link_no_editor);
    }
}
