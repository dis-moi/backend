<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'product.name',
            ])
            ->add('category', TextType::class, [
                'required' => false,
                'label' => 'product.category',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'product.description',
            ])
            ->add('offer', OfferType::class, [
                'required' => false,
                'label' => 'product.offer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Product',
        ]);
    }
}
