<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilder;

class ContributorAdminController extends AdminController
{
    public function createEntityFormBuilder($entity, $view): FormBuilder
    {
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        $contributorId = $entity->getId() ?? 0;

        $options = $formBuilder->get('starredNotice')->getOptions();
        unset($options['choice_loader']);
        $options['query_builder'] = static function (EntityRepository $repo) use ($contributorId) {
            return $repo->createQueryBuilder('n')
                ->where('n.contributor = :contributorId')
                ->setParameter('contributorId', $contributorId);
        };
        $formBuilder->add('starredNotice', EntityType::class, $options);

        return $formBuilder;
    }
}
