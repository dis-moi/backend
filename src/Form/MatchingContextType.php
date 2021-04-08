<?php

declare(strict_types=1);

namespace App\Form;

use App\Controller\AdminController;
use App\Entity\DomainName;
use App\Entity\DomainsSet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchingContextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                        'data-domains' => implode(',', array_map(
                            function (DomainName $domainName) {
                                return $domainName->getPrettyName();
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
            ->add('xpath', TextType::class, [
                'required' => false,
                'label' => 'matchingContexts.xpath',
                'help' => 'matchingContexts.xpath_help',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'matchingContexts.description',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\MatchingContext',
        ]);
    }
}
