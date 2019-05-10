<?php

namespace AppBundle\Form;

use AppBundle\Helper\NoticeIntention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntentionType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'Approval' => NoticeIntention::APPROVAL(),
                'Disapproval' => NoticeIntention::DISAPPROVAL(),
                'Information' => NoticeIntention::INFORMATION(),
                'Alternative' => NoticeIntention::ALTERNATIVE(),
                'Other' => NoticeIntention::OTHER(),
            ),
            'empty_data' =>  NoticeIntention::getDefault(),
        ));
    }

    /**
     * @return mixed
     */
    public function getParent(){
        return ChoiceType::class;
    }
}
