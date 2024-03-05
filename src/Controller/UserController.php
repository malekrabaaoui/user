<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SecondUserType;
use App\Form\LoginUserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;










#[Route('/user')]
class UserController extends AbstractController
{


    #[Route('/showemployee', name: 'app_fetch_employee', methods: ['GET'])]
    public function fetchemployee(UserRepository $userRepository): Response
    {
        // Call the custom method in UserRepository to fetch users with role "client"
        $users = $userRepository->findByRole('employee');
    
        return $this->render('user/fetchemployee.html.twig', [
            'users' => $users,
        ]);
    }

    



    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $user->setRole('client'); // Set default role here
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain-text password before persisting the user
            $plainPassword = $user->getPlainPassword();
            $hashedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    
    #[Route('/new_employee', name: 'app_user_new_employee', methods: ['GET', 'POST'])]
    public function new_employee(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $user->setRole('employee'); // Set default role here
        $user->setIdanimal(0); // Set default role here
        $form = $this->createForm(SecondUserType::class, $user); // Change UserType to SecondUserType
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain-text password before persisting the user
            $plainPassword = $user->getPlainPassword();
            $hashedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_fetch_employee', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('user/new_employee.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    




    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }





    #[Route('/{id}/edit_employee', name: 'app_user_edit_employee', methods: ['GET', 'POST'])]
    public function edit_employee(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SecondUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fetch_employee', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit_employee.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }





    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }




}
