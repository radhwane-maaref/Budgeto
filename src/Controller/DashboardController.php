<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\ExpensesRepository;
use App\Repository\BudgetRepository;
use App\Repository\SavingGoalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord de l'utilisateur connecté.
     * Récupère les 5 dernières dépenses et le total du mois en cours.
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ExpensesRepository $expensesRepository, BudgetRepository $budgetRepository,SavingGoalRepository $savingGoalRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Récupère les 5 dernières dépenses de l'utilisateur
        $recentExpenses = $expensesRepository->findBy(['user' => $user], ['date' => 'DESC'], 5);

        // Récupère les totaux et autres données
        $totalExpenses = $expensesRepository->sumTotalExpenses($user);
        $totalIncome = $budgetRepository->sumTotalIncome($user);
        $balance = $totalIncome - $totalExpenses;
        $goals = $savingGoalRepository->findBy(['user' => $user]);
        $expensesByCategory = $expensesRepository->sumExpensesByCategory($user);
        
        // Récupère le total des dépenses pour le mois en cours
        $totalThisMonth = $expensesRepository->getTotalForCurrentMonth($user);

        return $this->render('dashboard/index.html.twig', [
            'expenses' => $recentExpenses,
            'recentExpenses' => $recentExpenses,  // Keeping both variable names for compatibility
            'totalThisMonth' => $totalThisMonth,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'balance' => $balance,
            'savingGoals' => $goals,
            'categories' => $expensesByCategory,
        ]);
    }
}
