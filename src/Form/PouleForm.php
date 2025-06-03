<?php

// src/Form/PouleForm.php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Poule;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PouleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tournoi = $options['tournoi']; // 👈 récupère l'option personnalisée

        $builder
            ->add('numero')
            ->add('equipes', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'query_builder' => function (EntityRepository $er) use ($tournoi) {
                    return $er->createQueryBuilder('e')
                        ->leftJoin('e.poule', 'p')
                        ->where('p IS NULL')
                        ->andWhere('e.tournoi = :tournoi')
                        ->setParameter('tournoi', $tournoi);
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poule::class,
            'tournoi' => null, // 👈 option personnalisée autorisée ici
        ]);
    }
}
