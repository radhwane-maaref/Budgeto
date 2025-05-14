<?php

namespace App\Controller;

use App\Entity\SavingGoal;
use App\Form\SavingGoalForm;
use App\Repository\SavingGoalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/saving_goal')]
final class SavingGoalController extends AbstractController
{
    #[Route(name: 'app_saving_goal_index', methods: ['GET'])]
    public function index(SavingGoalRepository $savingGoalRepository): Response
    {
        return $this->render('saving_goal/index.html.twig', [
            'saving_goals' => $savingGoalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_saving_goal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $goal = new SavingGoal();
        $form = $this->createForm(SavingGoalForm::class, $goal);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $goal->setUser($this->getUser());
            $goal->setCurrentAmount(0);
            $em->persist($goal);
            $em->flush();
    
            return $this->redirectToRoute('app_saving_goal_index');
        }
    
        return $this->render('saving_goal/new.html.twig', [
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_saving_goal_show', methods: ['GET'])]
    public function show(SavingGoal $savingGoal): Response
    {
        return $this->render('saving_goal/show.html.twig', [
            'saving_goal' => $savingGoal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_saving_goal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SavingGoal $savingGoal, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SavingGoalForm::class, $savingGoal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_saving_goal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('saving_goal/edit.html.twig', [
            'saving_goal' => $savingGoal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_saving_goal_delete', methods: ['POST'])]
    public function delete(Request $request, SavingGoal $savingGoal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$savingGoal->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($savingGoal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_saving_goal_index', [], Response::HTTP_SEE_OTHER);
    }
}
