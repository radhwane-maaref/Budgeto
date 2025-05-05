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
    #[Route(name: 'app_expenses_index', methods: ['GET'])]
    public function index(ExpensesRepository $expensesRepository): Response
    {
        $user = $this->getUser();
        return $this->render('expenses/index.html.twig', [
            'expenses' => $expensesRepository->findBy(['user' => $user]),
        ]);
    }

    #[Route('/new', name: 'app_expenses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute("app_login");
        } else {
            $expense = new Expenses();
            $expense->setUser($user);
            $expense->setDate(new \DateTime());

            $form = $this->createForm(ExpensesForm::class, $expense, [
                'user' => $this->getUser(),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $budget = $expense->getBudget();
                if ($budget && $budget->getAmount() >= $expense->getAmount()) {
                    $newRemaining = $budget->getAmount() - $expense->getAmount();
                    $budget->setAmount($newRemaining);
                    $entityManager->persist($expense);
                    $entityManager->flush();
                    return $this->redirectToRoute('app_expenses_index', [], Response::HTTP_SEE_OTHER);
                } else {
                    $this->addFlash('error', 'Budget amount is insufficient for this expense!');
                    return $this->render('expenses/new.html.twig', [
                        'expense' => $expense,
                        'form' => $form,
                    ]);
                }
            }

            return $this->render('expenses/new.html.twig', [
                'expense' => $expense,
                'form' => $form,
            ]);
        }
    }

    #[Route('/{id}', name: 'app_expenses_show', methods: ['GET'])]
    public function show(Expenses $expense): Response
    {
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to view this expense.');
        }

        return $this->render('expenses/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expenses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expenses $expense, EntityManagerInterface $entityManager): Response
    {
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to edit this expense.');
        }

        $form = $this->createForm(ExpensesForm::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_expenses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expenses/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expenses_delete', methods: ['POST'])]
    public function delete(Request $request, Expenses $expense, EntityManagerInterface $entityManager): Response
    {
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to delete this expense.');
        }

        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_expenses_index', [], Response::HTTP_SEE_OTHER);
    }
}
