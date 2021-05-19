<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Locales;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                Locales::getName('fr') => 'fr',
                Locales::getName('en') => 'en',
                Locales::getName('br') => 'br',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
