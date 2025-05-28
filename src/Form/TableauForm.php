<?php

namespace App\Form;

use App\Entity\Tableau;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableauForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule')
            ->add('niveau', ChoiceType::class, [
                'choices' => [
                    'N1' => '1',
                    'N2' => '2',
                    'N3' => '3',
                    'N4' => '4',
                ]
            ])
            ->add('age',ChoiceType::class, [
                'choices' => [
                    '+19' => '19',
                    '+50' => '50',
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                ],
                'expanded' => false, // liste déroulante
                'multiple' => false, // un seul choix possible
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tableau::class,
        ]);
    }
}
