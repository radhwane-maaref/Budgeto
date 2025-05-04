<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\Expenses;
use App\Repository\BudgetRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpensesForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('amount')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category',
            ])
            ->add('budget', EntityType::class, [
                'class' => Budget::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a budget',
                'query_builder' => function (BudgetRepository $repo) use ($user) {
                    return $repo->createQueryBuilder('b')
                        ->where('b.user = :user')
                        ->setParameter('user', $user);
                },
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expenses::class,
            'user' => null,
        ]);
    }
}
