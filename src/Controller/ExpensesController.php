<?php

namespace App\Controller;

use App\Entity\Expenses;
use App\Form\ExpensesForm;
use App\Repository\ExpensesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expenses')]
final class ExpensesController extends AbstractController
{
    /**
     * Affiche la liste des dépenses de l'utilisateur connecté.
     * Récupère toutes les dépenses associées à l'utilisateur et les passe à la vue.
     */
    #[Route(name: 'app_expenses_index', methods: ['GET'])]
    public function index(ExpensesRepository $expensesRepository): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupère toutes les dépenses associées à l'utilisateur
        return $this->render('expenses/index.html.twig', [
            'expenses' => $expensesRepository->findBy(['user' => $user]),
        ]);
    }

    /**
     * Crée une nouvelle dépense pour l'utilisateur connecté.
     * Affiche le formulaire de création et traite la soumission, en vérifiant le budget disponible.
     */
    #[Route('/new', name: 'app_expenses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Vérifie si l'utilisateur est connecté
        if (!$user) {
            // Si non, redirige vers la page de connexion
            return $this->redirectToRoute("app_login");
        } else {
            // Crée une nouvelle dépense
            $expense = new Expenses();
            $expense->setUser($user);
            $expense->setDate(new \DateTime());

            // Crée le formulaire de création
            $form = $this->createForm(ExpensesForm::class, $expense, [
                'user' => $this->getUser(),
            ]);
            $form->handleRequest($request);

            // Vérifie si le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // Récupère le budget associé à la dépense
                $budget = $expense->getBudget();
                
                // Vérifie si le budget est suffisant pour la dépense
                if ($budget && $budget->getAmount() >= $expense->getAmount()) {
                    // Met à jour le montant restant du budget
                    $newRemaining = $budget->getAmount() - $expense->getAmount();
                    $budget->setAmount($newRemaining);
                    
                    // Enregistre la dépense et le budget mis à jour
                    $entityManager->persist($expense);
                    $entityManager->flush();
                    
                    // Redirige vers la liste des dépenses
                    return $this->redirectToRoute('app_expenses_index', [], Response::HTTP_SEE_OTHER);
                } else {
                    // Ajoute un message d'erreur si le budget est insuffisant
                    $this->addFlash('error', 'Le montant de la dépense dépasse le budget disponible.');
                    
                    // Affiche le formulaire avec les erreurs
                    return $this->render('expenses/new.html.twig', [
                        'expense' => $expense,
                        'form' => $form,
                    ]);
                }
            }

            // Affiche le formulaire de création
            return $this->render('expenses/new.html.twig', [
                'expense' => $expense,
                'form' => $form,
            ]);
        }
    }

    /**
     * Affiche les détails d'une dépense.
     * Vérifie si l'utilisateur connecté est autorisé à afficher la dépense.
     */
    #[Route('/{id}', name: 'app_expenses_show', methods: ['GET'])]
    public function show(Expenses $expense): Response
    {
        // Vérifie si l'utilisateur connecté est autorisé à afficher la dépense
        if ($expense->getUser() !== $this->getUser()) {
            // Si non, lance une exception d'accès refusé
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à afficher cette dépense.');
            throw $this->createAccessDeniedException('You are not allowed to view this expense.');
        }

        return $this->render('expenses/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    /**
     * Modifie une dépense existante.
     * Vérifie si l'utilisateur connecté est autorisé à modifier la dépense, affiche le formulaire d'édition et traite la soumission.
     */
    #[Route('/{id}/edit', name: 'app_expenses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expenses $expense, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur connecté est autorisé à modifier la dépense
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette dépense.');
        }

        // Crée et gère le formulaire d'édition
        $form = $this->createForm(ExpensesForm::class, $expense);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistre les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_expenses_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire d'édition
        return $this->render('expenses/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une dépense après validation du token CSRF.
     * Vérifie si l'utilisateur connecté est autorisé à supprimer la dépense.
     */
    #[Route('/{id}', name: 'app_expenses_delete', methods: ['POST'])]
    public function delete(Request $request, Expenses $expense, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur connecté est autorisé à supprimer la dépense
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette dépense.');
        }

        // Vérifie le token CSRF avant suppression
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_expenses_index', [], Response::HTTP_SEE_OTHER);
    }
}
