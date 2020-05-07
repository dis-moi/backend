<?php

namespace AppBundle\Form;

use AppBundle\Controller\AdminController;
use AppBundle\Entity\DomainName;
use AppBundle\Entity\DomainsSet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchingContextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exampleUrl', TextType::class, [
                'required' => false,
                'label' => 'matchingContexts.exampleUrl',
            ])
            ->add('domainsSets', EntityType::class, [
                'class' => DomainsSet::class,
                'label' => 'matchingContexts.domainsSets',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'data-widget' => 'select2',
                ],
                'choice_attr' => function (DomainsSet $domainsSet) {
                    return [
                        'data-domains' => join(',', array_map(
                            function (DomainName $domainName) {
                                return $domainName->getName();
                            },
                            $domainsSet->getDomains()->toArray()
                        )),
                    ];
                },
                'query_builder' => [AdminController::class, 'getDomainsSetQueryBuilder'],
            ])
            ->add('domainNames', EntityType::class, [
                'class' => DomainName::class,
                'label' => 'matchingContexts.domainNames',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'data-widget' => 'select2',
                ],
                'query_builder' => [AdminController::class, 'getDomainNameQueryBuilder'],
            ])
            ->add('urlRegex', TextareaType::class, [
                'label' => 'matchingContexts.urlRegex',
            ])
            ->add('excludeUrlRegex', TextareaType::class, [
                'required' => false,
                'label' => 'matchingContexts.excludeUrlRegex',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'matchingContexts.description',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\MatchingContext',
        ]);
    }
}
