<?php

namespace AppBundle\Form;

use AppBundle\Entity\DomainName;
use AppBundle\Entity\DomainsSet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchingContextType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exampleUrl')
            ->add('domainsSets', EntityType::class, [
                'class' => DomainsSet::class,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'data-widget' => 'select2'
                ],
                'choice_attr' => function (DomainsSet $domainsSet) {
                    return [
                        'data-domains' => join(',', array_map(
                            function (DomainName $domainName) {
                                return $domainName->getName();
                            },
                            $domainsSet->getDomains()->toArray()
                        ))
                    ];
                }
            ])
            ->add('domainNames', EntityType::class, [
                'class' => DomainName::class,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'data-widget' => 'select2'
                ]
            ])
            ->add('urlRegex', TextType::class)
            ->add('excludeUrlRegex', TextType::class, [
                'required' => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MatchingContext'
        ));
    }
}
