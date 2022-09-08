<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account', methods:['GET'])]
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }

    #[Route('/account/edit', name: 'app_account_edit', methods:['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            $this->addFlash('error', 'You are not connected !');
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $em->flush();

            $this->addFlash('success', 'Profile successfully updated !');
        
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
