<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\Tip;
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

    #[Route('/exercises')]
    public function exercisesTable(): Response
    {
        $exercises = $this->doctrine->getRepository(Exercise::class)->findAll();
        return $this->render('exercises/table.html.twig',  [
            'exercises' => $exercises,
        ]);
    }
    #[Route('/exercises/add', methods: array('GET', 'POST'))]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $exercise = new Exercise();

        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $exercise = $form->getData();
            $tip = new Tip();
            $tip->setName('sjdadn');
            $exercise->setTip($tip);
            $entityManager->persist($tip);
            $entityManager->persist($exercise);
            $entityManager->flush();

            // ... perform some action, such as saving the task to the database

        }

        return $this->render('exercises/addExercisePage.html.twig', [
            'form' => $form
        ]);
    }

}