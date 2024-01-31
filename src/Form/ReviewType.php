<?php

namespace App\Form;

use App\Entity\Review;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Votre pseudo',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Lachez vous',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10
                    ]),
                ]
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Votre note',
                'choices' => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1,
                ],
                'multiple' => false,
                'expanded' => false
            ])
            ->add('reactions', ChoiceType::class, [
                'label' => 'Ce film vous a fait :',
                'choices' => [
                    'Rire' => 'laugh',
                    'Pleurer' => 'cry',
                    'Réfléchir' => 'think',
                    'Dormir' => 'sleep',
                    'Rêver' => 'dream',
                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('watchedAt', DateType::class, [
                'label' => 'Vous avez ce film le ',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'data_class' => DateTimeImmutable::class,
                'required' => false,
                'mapped' => false,
                // 'empty_data' => "",
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
