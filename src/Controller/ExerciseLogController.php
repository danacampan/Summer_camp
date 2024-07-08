<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\ExerciseLog;
use App\Form\Type\ExerciseLogType;
use App\Form\Type\ExerciseType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function exercisesLogs(): Response
    {
        $exercise_logs = $this->doctrine->getRepository(ExerciseLog::class)->findAll();
        return $this->render('exerciseLogs/table.html.twig', [
            'exercise_logs' => $exercise_logs,
        ]);
    }
    #[Route('/log',name:'create_log', methods: array('GET', 'POST'))]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $log = new ExerciseLog();

        $form = $this->createForm(ExerciseLogType::class, $log);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $log = $form->getData();

            $entityManager->persist($log);
            $entityManager->flush();


        }
        return $this->render('exerciseLogs/addExerciseLog.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/log/{id}',name: 'edit_log', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ExerciseLog $log)
    {

        $form = $this->createForm(ExerciseLogType::class, $log, [
            'action' => $this->generateUrl('edit_log', ['id' => $log->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $log = $form->getData();
            $entityManager->persist($log);
            $entityManager->flush();
            return $this->render('exerciseLogs/table.html.twig');


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