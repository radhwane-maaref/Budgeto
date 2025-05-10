<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\Expenses;
use App\Repository\BudgetRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class ExpensesForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('amount',NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Amount cannot be blank.',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Amount must be a positive value.',
                    ]),
                ],
            ])
            ->add('description',TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Description cannot be blank.',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 255,
                        'minMessage' => 'Description should be at least {{ limit }} characters long.',
                        'maxMessage' => 'Description cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
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
