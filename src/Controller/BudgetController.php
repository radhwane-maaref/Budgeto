<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetForm;
use App\Repository\BudgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget')]
final class BudgetController extends AbstractController
{
    #[Route(name: 'app_budget_index', methods: ['GET'])]
    /**
     * Affiche la liste des budgets de l'utilisateur connecté.
     * Récupère tous les budgets associés à l'utilisateur et les passe à la vue.
     */
    public function index(BudgetRepository $budgetRepository): Response
    {
        $user = $this->getUser();
        $budgets = $budgetRepository->findBy(['user' => $user]);

        return $this->render('budget/index.html.twig', [
            'budgets' => $budgets,
        ]);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
    /**
     * Crée un nouveau budget pour l'utilisateur connecté.
     * Affiche le formulaire de création et traite la soumission.
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $budget = new Budget();
        $user = $this->getUser();
        $budget->setUser($user);
        $budget->setDate(new \DateTime());
        $form = $this->createForm(BudgetForm::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($budget);
            $entityManager->flush();

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('budget/new.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_budget_show', methods: ['GET'])]
    /**
     * Affiche le détail d'un budget spécifique.
     * @param Budget $budget Le budget à afficher
     */
    public function show(Budget $budget): Response
    {
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
    /**
     * Permet de modifier un budget existant.
     * Affiche le formulaire d'édition et traite la soumission.
     * @param Request $request La requête HTTP
     * @param Budget $budget Le budget à modifier
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités Doctrine
     */
    public function edit(Request $request, Budget $budget, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BudgetForm::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('budget/edit.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_budget_delete', methods: ['POST'])]
    /**
     * Supprime un budget après validation du token CSRF.
     * @param Request $request La requête HTTP
     * @param Budget $budget Le budget à supprimer
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités Doctrine
     */
    public function delete(Request $request, Budget $budget, EntityManagerInterface $entityManager): Response
    {
        // Check CSRF token
        if ($this->isCsrfTokenValid('delete' . $budget->getId(), $request->get('_token'))) {
            // Remove the budget entity
            $entityManager->remove($budget);
            $entityManager->flush();
        }

        // Redirect after deletion
        return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
    }
}
