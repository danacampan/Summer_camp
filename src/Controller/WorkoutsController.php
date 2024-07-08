<?php

namespace App\Controller;

use App\Entity\Workout;
use App\Form\Type\WorkoutType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkoutsController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/workouts', name: 'workouts')]
    public function workout_list(): Response
    {
        $workouts = $this->doctrine->getRepository(Workout::class)->findAll();
        return $this->render('workouts/table.html.twig', [
            'workouts' => $workouts,
        ]);
    }


    #[Route('/workout',name:'create_workout', methods: array('GET', 'POST'))]
    public function new_workout(Request $request, EntityManagerInterface $entityManager)
    {
        $workout = new Workout();
        $form = $this->createForm(WorkoutType::class, $workout);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = $form->getData();
            $workout->setUser('Dana');
            $entityManager->persist($workout);
            $entityManager->flush();
        }
        return $this->render('workouts/addWorkout.html.twig', ['form' => $form]);
    }

    #[Route('/workout/{id}', name: 'edit_workout', methods: array('GET', 'PUT'))]
    public function edit(Request $request, Workout $workout, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(WorkoutType::class, $workout, [
            'action' => $this->generateUrl('edit_workout', ['id' => $workout->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = $form->getData();
            $entityManager->persist($workout);
            $entityManager->flush();
            #return $this->render('exercises/editSuccess.html.twig');
        }
        return $this->render('workouts/editWorkout.html.twig', ['form' => $form]);
    }
    public function delete(Request $request, Workout $workout, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('delete' . $workout->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workout);
            $entityManager->flush();

        }
        return $this->redirectToRoute('workouts');
    }
}