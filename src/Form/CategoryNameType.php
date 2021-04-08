<?php

declare(strict_types=1);

namespace App\Form;

use App\Domain\Model\Enum\CategoryName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryNameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => CategoryName::getChoices(),
            'expanded' => true,
            'multiple' => true,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
