<?php

declare(strict_types=1);

namespace App\Form;

use App\Helper\NoticeVisibility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisibilityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => NoticeVisibility::getChoices(),
            'empty_data' => NoticeVisibility::getDefault(),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
