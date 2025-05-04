<?php

namespace App\Controller;

use App\Repository\ExpensesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ExpensesRepository $expensesRepository): Response
    {
            /** @var User $user */
    $user = $this->getUser();

    // Get last 5 expenses for the current user
    $recentExpenses = $expensesRepository->findBy(
        ['user' => $user],
        ['date' => 'DESC'],
        5 // limit
    );

    $totalThisMonth = $expensesRepository->getTotalForCurrentMonth($user);

    return $this->render('dashboard/index.html.twig', [
        'recentExpenses' => $recentExpenses,
        'totalThisMonth' => $totalThisMonth,
    ]);
}
}