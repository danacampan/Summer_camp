<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\ExerciseLog;
use App\Entity\Workout;
use App\Form\Type\ExerciseLogType;
use App\Form\Type\ExerciseType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExerciseLogController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/logs', name: 'exercise_logs')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $exerciseLogRepository = $entityManager->getRepository(ExerciseLog::class);
        $workoutRepository = $entityManager->getRepository(Workout::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $exerciseLogs = $exerciseLogRepository->findAll();
            $workouts = $workoutRepository->findAll();
        } else {
            $workouts = $workoutRepository->findBy(['user' => $user]);
            $workoutIds = array_map(function($workout) {
                return $workout->getId();
            }, $workouts);
            $exerciseLogs = $exerciseLogRepository->findBy(['workout' => $workoutIds]);
        }


        $groupedLogs = [];

        foreach ($exerciseLogs as $log) {
            $workoutId = $log->getWorkout()->getId();
            if (!isset($groupedLogs[$workoutId])) {
                $groupedLogs[$workoutId] = [
                    'workout' => $log->getWorkout(),
                    'logs' => [],
                ];
            }
            $groupedLogs[$workoutId]['logs'][] = $log;
        }
        foreach ($workouts as $workout) {
            $workoutId = $workout->getId();
            if (!isset($groupedLogs[$workoutId])) {
                $groupedLogs[$workoutId] = [
                    'workout' => $workout,
                    'logs' => [],
                ];
            }
        }

        return $this->render('exerciseLogs/table.html.twig', [
            'groupedLogs' => $groupedLogs,
            'workouts' => $workouts,
        ]);
    }

    #[Route('/log',name:'create_log', methods: array('GET', 'POST'))]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $workoutRepository = $entityManager->getRepository(Workout::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $workouts = $workoutRepository->findAll();
        } else {
            $workouts = $workoutRepository->findBy(['user' => $user]);
        }

        $log = new ExerciseLog();
        $form = $this->createForm(ExerciseLogType::class, $log, [
            'workouts' => $workouts,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $log = $form->getData();
            $existingLog = $entityManager->getRepository(ExerciseLog::class)->findOneBy([
                'workout' => $log->getWorkout(),
                'exercise' => $log->getExercise(),
            ]);
            if ($existingLog) {
                $form->get('Exercise')->addError(new FormError('Acest exercițiu a fost deja adăugat pentru acest antrenament.'));
            } else {
                $entityManager->persist($log);
                $entityManager->flush();
                $this->addFlash('success', 'Exercițiul a fost creat cu succes!');
                return $this->redirectToRoute('exercise_logs');
            }
        }
        return $this->render('exerciseLogs/addExerciseLog.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/log/{id}',name: 'edit_log', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ExerciseLog $log)
    {
        $user = $this->getUser();
        $workoutRepository = $entityManager->getRepository(Workout::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $workouts = $workoutRepository->findAll();
        } else {
            $workouts = $workoutRepository->findBy(['user' => $user]);
        }

        $form = $this->createForm(ExerciseLogType::class, $log, [
            'action' => $this->generateUrl('edit_log', ['id' => $log->getId()]),
            'method' => 'PUT',
            'workouts' => $workouts,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $log = $form->getData();
            $entityManager->persist($log);
            $entityManager->flush();
            $this->addFlash('success', 'Exercițiul a fost editat cu succes!');
            return $this->redirectToRoute('exercise_logs');
        }
        return $this->render('exerciseLogs/editExerciseLog.html.twig', ['form'=>$form]);
    }

    #[Route('/log/{id}',name: 'delete_log', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, ExerciseLog $log)
    {
        if ($this->isCsrfTokenValid('delete' . $log->getId(), $request->request->get('_token'))) {
            $entityManager->remove($log);
            $entityManager->flush();
        }
        return $this->redirectToRoute('exercise_logs');
    }


}