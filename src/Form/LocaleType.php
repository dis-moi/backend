<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Locales;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    /**
     * @var string[]
     */
    private $supportedLocales = [];

    /**
     * LocaleType constructor.
     *
     * @param string[] $supportedLocales
     */
    public function __construct(array $supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_combine(
                array_map(
                    function ($locale) {
                        return Locales::getName($locale);
                    },
                    $this->supportedLocales
                ),
                $this->supportedLocales
            ),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
