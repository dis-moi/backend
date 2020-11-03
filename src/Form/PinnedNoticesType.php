<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Notice;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PinnedNoticesType extends EasyAdminAutocompleteType
{
    private $givenNoticeIds;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
          ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event) {
              $this->givenNoticeIds = $event->getData()['autocomplete'];
          })
            ->addEventListener(FormEvents::SUBMIT, function (SubmitEvent $event) {
                $form = $event->getForm();

                /** @var ArrayCollection $formData */
                $formData = $form->getData();

                /** @var Notice $notice */
                foreach ($formData as $notice) {
                    foreach ($this->givenNoticeIds as $sort => $id) {
                        if ((int) $id === $notice->getId()) {
                            $notice->setPinnedSort((int) $sort);
                        }
                    }
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'multiple' => true,
        ]);
    }
}
