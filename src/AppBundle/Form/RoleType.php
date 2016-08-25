<?php

namespace AppBundle\Form;

use AppBundle\Entity\ContributorRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'Auteur' => ContributorRole::AUTHOR_ROLE(),
                'Editeur' => ContributorRole::EDITOR_ROLE(),
            ),
            'empty_data' =>  ContributorRole::getDefault()
        ));
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
