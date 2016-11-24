<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Editor;
use AppBundle\Entity\Resource;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadResourceData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $resource_link_quechoisir = new Resource();
        $resource_link_quechoisir->setUrl('quechoisir.fr/iphone-is-the-best-phone');
        $resource_link_quechoisir->setLabel('Que Choisir: Iphone is the best phone');
        $resource_link_quechoisir->setEditor($this->getReference('editor_quechoisir'));
        $manager->persist($resource_link_quechoisir);
        $manager->flush();
        $this->addReference('resource_link_quechoisir', $resource_link_quechoisir);

        $resource_link_marianne = new Resource();
        $resource_link_marianne->setUrl('marianne.fr/great-article');
        $resource_link_marianne->setLabel('Marianne: Super article');
        $resource_link_marianne->setEditor($this->getReference('editor_marianne'));
        $manager->persist($resource_link_marianne);
        $manager->flush();
        $this->addReference('resource_link_marianne', $resource_link_marianne);

        $resource_link_huffington = new Resource();
        $resource_link_huffington->setUrl('huffington.fr/trump-will-save-us-all');
        $resource_link_huffington->setLabel('Huffington Post: How Trump will make america great again');
        $resource_link_huffington->setEditor($this->getReference('editor_huffington'));
        $manager->persist($resource_link_huffington);
        $manager->flush();
        $this->addReference('resource_link_huffington', $resource_link_huffington);

        $resource_link_no_editor = new Resource();
        $resource_link_no_editor->setUrl('http://noeditor.com/myresource');
        $resource_link_no_editor->setLabel('A resource without editor');
        $manager->persist($resource_link_no_editor);
        $manager->flush();
        $this->addReference('resource_with_no_editor', $resource_link_no_editor);
    }

    public function getDependencies()
    {
        return [LoadEditorData::class];
    }
}
