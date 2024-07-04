<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
    #[Route('/', methods: 'GET')]
    public function Homepage(): Response
    {
        return $this->render('homepage/homepage.html.twig',[]);
    }
}