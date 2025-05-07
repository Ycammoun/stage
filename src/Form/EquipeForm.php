<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Tableau;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class EquipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('nom')
            ->add('joueurs', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function (Utilisateur $user) {
                    return $user->getPrenom() . ' ' . $user->getNom();
                },
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Count([
                        'max' => 2,
                        'maxMessage' => 'Une équipe ne peut pas avoir plus de {{ limit }} joueurs.',
                    ]),
                ],
            ])

            ->add('tableau', EntityType::class, [
                'class' => Tableau::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
