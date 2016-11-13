<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Editor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEditorData extends AbstractFixture
{
    const QUE_CHOISIR = 'Que Choisir';

    const MARIANNE = 'Marianne';

    const HUFFINGTON_POST = 'Huffington Post';

    public function load(ObjectManager $manager)
    {
        $editor_quechoisir = new Editor();
        $editor_quechoisir->setUrl('quechoisir.fr');
        $editor_quechoisir->setLabel(self::QUE_CHOISIR);
        $manager->persist($editor_quechoisir);
        $manager->flush();
        $this->addReference('editor_quechoisir', $editor_quechoisir);

        $editor_marianne = new Editor();
        $editor_marianne->setUrl('marianne.fr');
        $editor_marianne->setLabel(self::MARIANNE);
        $manager->persist($editor_marianne);
        $manager->flush();
        $this->addReference('editor_marianne', $editor_marianne);

        $editor_huffington = new Editor();
        $editor_huffington->setUrl('huffington.fr');
        $editor_huffington->setLabel(self::HUFFINGTON_POST);
        $manager->persist($editor_huffington);
        $manager->flush();
        $this->addReference('editor_huffington', $editor_huffington);
    }
}
