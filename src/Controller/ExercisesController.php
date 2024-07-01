<?php

namespace App\Controller;

use App\Entity\Exercise;
use Doctrine\Persistence\ManagerRegistry;
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

}