<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Repository\ExpensesRepository;
use App\Repository\BudgetRepository;
use App\Repository\SavingGoalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ExpensesRepository $expensesRepository, BudgetRepository $budgetRepository,SavingGoalRepository $savingGoalRepository): Response
    {
        $user = $this->getUser();

        $recentExpenses = $expensesRepository->findBy(['user' => $user], ['date' => 'DESC'], 5);

        $totalExpenses = $expensesRepository->sumTotalExpenses($user);
        $totalIncome = $budgetRepository->sumTotalIncome($user);
        $balance = $totalIncome - $totalExpenses;
        $goals = $savingGoalRepository->findBy(['user' => $this->getUser()]);
        $expensesByCategory = $expensesRepository->sumExpensesByCategory($user);

        return $this->render('dashboard/index.html.twig', [
            'expenses' => $recentExpenses,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'balance' => $balance,
            'savingGoals'=>$goals,
            'categories' => $expensesByCategory,
        ]);
    }
}
