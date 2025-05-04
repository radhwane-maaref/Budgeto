<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Groceries',
            'Rent',
            'Utilities',
            'Transport',
            'Entertainment',
            'Dining Out',
            'Health & Medical',
            'Insurance',
            'Savings',
            'Debt Repayment',
            'Education',
            'Subscriptions',
            'Gifts',
            'Travel',
            'Clothing',
            'Personal Care',
            'Miscellaneous',
        ];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
        }

        $manager->flush();
    
    }
}
