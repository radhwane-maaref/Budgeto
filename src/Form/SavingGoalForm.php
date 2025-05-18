<?php

namespace App\Form;

use App\Entity\SavingGoal;
use BcMath\Number;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SavingGoalForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('targetAmount')
            ->add('currentAmount', NumberType::class, [
                'data' => 0,  
                'attr' => [
                    'min' => 0,       
                    'step' => '0.01',  
                ],
                'html5' => true,     
            ])
            ->add('deadline')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SavingGoal::class,
        ]);
    }
}
