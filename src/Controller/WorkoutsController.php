<?php

namespace App\Controller;

use App\Entity\User;
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
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $workouts = $this->doctrine->getRepository(Workout::class)->findAll();
        } else {
            $workouts = $this->doctrine->getRepository(Workout::class)->findBy(['user' => $user]);
        }
        return $this->render('workouts/table.html.twig', [
            'workouts' => $workouts,
        ]);
    }


    #[Route('/workout',name:'create_workout', methods: array('GET', 'POST'))]
    public function new_workout(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $userRepository = $this->doctrine->getRepository(User::class);
        $users = $this->isGranted('ROLE_ADMIN') ? $userRepository->findAll() : [$user];

        $workout = new Workout();
        $workout->setUser($user);
        $form = $this->createForm(WorkoutType::class, $workout, ['users' => $users]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = $form->getData();
            $entityManager->persist($workout);
            $entityManager->flush();
            $this->addFlash('success', 'Antrenamentul a fost creat cu succes!');
            return $this->redirectToRoute('exercise_logs');
        }
        return $this->render('workouts/addWorkout.html.twig', ['form' => $form]);
    }
    #[Route('/workout/{id}', name: 'edit_workout', methods: array('GET', 'PUT'))]
    public function edit(Request $request, Workout $workout, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $userRepository = $this->doctrine->getRepository(User::class);
        $users = $this->isGranted('ROLE_ADMIN') ? $userRepository->findAll() : [$user];

        $form = $this->createForm(WorkoutType::class, $workout, [
            'action' => $this->generateUrl('edit_workout', ['id' => $workout->getId()]),
            'method' => 'PUT',
            'users' => $users,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = $form->getData();
            $entityManager->persist($workout);
            $entityManager->flush();
            $this->addFlash('success', 'Antrenamentul a fost editat cu succes!');
            return $this->redirectToRoute('exercise_logs');
        }
        return $this->render('workouts/editWorkout.html.twig', ['form' => $form]);
    }
    #[Route('/workout/{id}', name: 'delete_workout', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, Workout $workout, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('delete' . $workout->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workout);
            $entityManager->flush();

        }
        return $this->redirectToRoute('exercise_logs');
    }
}