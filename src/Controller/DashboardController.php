<?php

namespace App\Controller;

use App\Repository\ExpensesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
final class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord de l'utilisateur connecté.
     * Récupère les 5 dernières dépenses et le total du mois en cours.
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ExpensesRepository $expensesRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Récupère les 5 dernières dépenses de l'utilisateur
        $recentExpenses = $expensesRepository->findBy(
            ['user' => $user],
            ['date' => 'DESC'],
            5 // limite
        );

        // Récupère le total des dépenses pour le mois en cours
        $totalThisMonth = $expensesRepository->getTotalForCurrentMonth($user);

        return $this->render('dashboard/index.html.twig', [
            'recentExpenses' => $recentExpenses,
            'totalThisMonth' => $totalThisMonth,
        ]);
    }
}