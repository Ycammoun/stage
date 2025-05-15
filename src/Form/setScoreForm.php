<?php

namespace App\Form;



use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Correct import

use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class setScoreForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('score1',IntegerType::class,['label' => 'Score du joueur 1 :'])
            ->add('score2',IntegerType::class,['label' => 'Score du joueur 2 :'])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Score'
            ])
        ;

    }

}
