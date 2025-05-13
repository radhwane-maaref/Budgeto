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
    public function index(BudgetRepository $budgetRepository): Response
    {
        $user = $this->getUser();
        $budgets = $budgetRepository->findBy(['user' => $user]);

        return $this->render('budget/index.html.twig', [
            'budgets' => $budgets,
        ]);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
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
    public function show(Budget $budget): Response
    {
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
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
