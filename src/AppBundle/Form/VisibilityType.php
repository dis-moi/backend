<?php

namespace AppBundle\Form;

use AppBundle\Entity\RecommendationVisibility;
use AppBundle\Form\DataTransformer\StringToVisibilityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'Private' => RecommendationVisibility::PRIVATE_VISIBILITY(),
                'Public' => RecommendationVisibility::PUBLIC_VISIBILITY()
            ),
            'empty_data' =>  RecommendationVisibility::getDefault()
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new StringToVisibilityTransformer());
    }

    /**
     * @return mixed
     */
    public function getParent(){
        return ChoiceType::class;
    }
}
