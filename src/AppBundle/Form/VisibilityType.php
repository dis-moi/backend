<?php

namespace AppBundle\Form;

use AppBundle\Helper\NoticeVisibility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisibilityType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'Archived' => NoticeVisibility::ARCHIVED_VISIBILITY(),
                'Private' => NoticeVisibility::PRIVATE_VISIBILITY(),
                'Public' => NoticeVisibility::PUBLIC_VISIBILITY(),
            ),
            'empty_data' =>  NoticeVisibility::getDefault()
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
