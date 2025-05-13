<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalculTournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('nbTerrains', IntegerType::class, ['label' => 'Nombre de terrains :'])
            ->add('nbTableaux', IntegerType::class, ['label' => 'Nombre de tableaux :'])
            ->add('nbJoueurs',IntegerType::class,['label' => 'Nombre de joueurs :'])
            ;

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

}