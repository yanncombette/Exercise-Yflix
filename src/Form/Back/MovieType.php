<?php

namespace App\Form\Back;

use App\Entity\Genre;
use App\Entity\Movie;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du film'
            ])
            ->add('release_date', DateType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text',
                'input' => 'datetime',
                // 'data_class' => DateTimeImmutable::class,
                'required' => false,
                'mapped' => false,
            ]
            )
            ->add('duration')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Série' => 'Série',
                    'Film' => 'Film',
                ],
                'multiple' => false,
                'expanded' => true
            ])
            ->add('summary')
            ->add('synopsis')
            ->add('poster', UrlType::class, [
                'help' => 'Une url vers une image sur le web',
            ])
            ->add('rating', NumberType::class)
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
