<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;
use Liip\ImagineBundle\LiipImagineBundle;
use Symfony\Component\Security\Core\Security;

class PinsController extends AbstractController
{
    

    #[Route('/', name:'app_home', methods:['GET'])]
    
    public function index(PinRepository $repo): Response
    {
        $pins = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pins/index.html.twig', compact('pins'));
    }

    
    #[Route('/pins/{id<[0-9]+>}', name:'app_pins_show')]
    
    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    
    #[Route('/pins/{id<[0-9]+>}/edit', name:'app_pins_edit', methods:['GET', 'PUT'])]
    
    public function edit(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You are not connected !');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $em->flush();

            $this->addFlash('success', 'Pin successfully updated !');
        
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }
    
    
    #[Route('/pins/{id<[0-9]+>}/delete', name:'app_pins_delete', methods:['GET', 'DELETE'])]
    
    public function delete(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
        // if($this->isCsrfTokenValid('pin_deletion_' . $pin->getId(), $request->request->get('csrf_token'))) {

        $em->remove($pin);
        $em->flush();

        $this->addFlash('info', 'Pin successfully deleted !');
        // }
        return $this->redirectToRoute('app_home');
    }
    

    #[Route('/pins/create', name:'app_pins_create', methods:['GET', 'POST'])]
        
     public function create(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You are not connected !');
            return $this->redirectToRoute('app_home');
        }

        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $pin->setOwner($security->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created !');
            
            return $this->redirectToRoute('app_pins_show', ['id' => $pin->getId()]);
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
