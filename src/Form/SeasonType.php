<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'number',
                IntegerType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ],
                    'label' => 'Numéro de la saison',
                    'attr' => [
                        'placeholder' => 3,
                        'tata' => 'toto',
                    ],
                ]
            )
            ->add(
                'episodesNumber',
                IntegerType::class,
                [
                    'label' => 'Nombre d\'épisodes',
                ]
            )
            ->add('movie', EntityType::class, [
                'class' => Movie::class,
                'label' => 'Série',
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
