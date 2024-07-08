<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/users', name: 'users_list')]
    public function usersTable(): Response
    {
        $users = $this->doctrine->getRepository(User::class)->findAll();
        return $this->render('users/table.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user', name:'create_user',  methods: array('GET', 'POST'))]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();
            $user->setName('Dana');
            $user->setParola('dana');
            $user->setGender(0);
            $user->setBirthday(new \DateTime('now'));
            $entityManager->persist($user);
            $entityManager->flush();

            // ... perform some action, such as saving the task to the database

        }

        return $this->render('users/addUsersPage.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/user/{id}',name: 'edit_user', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $user)
    {
        $form = $this->createForm(UserType::class, $user, [
            'action'=>$this->generateUrl('edit_user', ['id'=>$user->getId()]),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render('users/editUserSuccess.html.twig', []);
        }
        return $this->render('users/editUserPage.html.twig', ['form'=>$form]);
    }
    #[Route('/user/{id}',name: 'delete_user', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, User $user)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

        }
        return $this->redirectToRoute('users_list');
    }


}