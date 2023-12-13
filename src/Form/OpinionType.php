<?php

namespace App\Form;

use App\Entity\Opinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class OpinionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score', ChoiceType::class, [
                'choices' => [
                    '😒' => -1,
                    '😐' => 0,
                    '😁' => 1,
                ],
                'expanded' => true,
                'label' =>false,
                'attr' => [
                    'class' => 'my-custom-class',
                    'onchange' => 'toggleInfoField()',
                ],

            ])
            ->add('info', TextareaType::class, [
                'required' => false,
                'label' =>false,

                'attr' => [
                    'class' => 'info-field',
                    'placeholder' => 'Tu wpisz opinię',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'submit-button',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Opinion::class,
        ]);
    }
}
