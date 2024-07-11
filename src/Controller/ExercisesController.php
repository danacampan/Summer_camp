<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\Tip;
use App\Entity\User;
use App\Form\Type\ExerciseType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExercisesController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/exercises', name: 'exercise_list')]
    public function exercisesTable(): Response
    {
        $exercises = $this->doctrine->getRepository(Exercise::class)->findAll();
        return $this->render('exercises/table.html.twig',  [
            'exercises' => $exercises,
        ]);
    }
    #[Route('/exercise', name:'create_exercise', methods: array('GET', 'POST'))]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $exercise = new Exercise();

        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $exercise = $form->getData();

            $entityManager->persist($exercise);
            $entityManager->flush();
            $this->addFlash('success', 'Exercițiul a fost creat cu succes!');
            return $this->redirectToRoute('exercise_list');

        }
        return $this->render('exercises/addExercisePage.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/exercise/{id}',name: 'edit_exercise', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Exercise $exercise)
    {

        $form = $this->createForm(ExerciseType::class, $exercise, [
            'action' => $this->generateUrl('edit_exercise', ['id' => $exercise->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $exercise = $form->getData();
            $entityManager->persist($exercise);
            $entityManager->flush();
            #return $this->render('exercises/editSuccess.html.twig');
            $this->addFlash('success', 'Exercițiul a fost editat cu succes!');
            return $this->redirectToRoute('exercise_list');


    }
        return $this->render('exercises/editExercisePage.html.twig', ['form'=>$form]);
}
    #[Route('/exercise/{id}',name: 'delete_exercise', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Exercise $exercise)
    {
        if ($this->isCsrfTokenValid('delete' . $exercise->getId(), $request->request->get('_token'))) {
            $entityManager->remove($exercise);
            $entityManager->flush();
        }
        return $this->redirectToRoute('exercise_list');
    }
}