<?php

namespace App\Controller;

use App\Entity\Emails;
use App\Form\EmailFormType;
use App\Repository\EmailsRepository;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagerieController extends AbstractController
{

    /**
     * @Route("/messagerie", name="app_messagerie")
    */    
    public function index(): Response
    {
        //si l'utilisateur existe et qu'il a vérifié son compte
        if($this->getUser() && $this->getUser()->isVerified())
        {
            return $this->render('messagerie/index.html.twig', [
                'controller_name' => 'MessagerieController',
            ]);
        }

        //si l'utilisateur n'existe pas encore
        if(!$this->getUser())
            $this->addFlash('error', 'Veuillez vous inscrire pour accéder à l\'application.');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/sendemail", name="app_sender")
     * 
    */
    public function sendEmail(Request $request, EntityManagerInterface $entityManager, Mail $mailService, EmailsRepository $emailRepo): Response
    {
        //si l'utilisateur existe et qu'il a vérifié son compte
        if($this->getUser() && $this->getUser()->isVerified())
        {
            $emailEntity = new Emails();
            //création du formulaire
            $form = $this->createForm(EmailFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $sourceEmail    = $this->getUser()->getEmail();
                $destinataire   = $form->get('destinataire')->getData();
                $sujet          = $form->get('sujet')->getData();
                $message        = $form->get('message')->getData();
                
                $context = [
                    'sourceEmail'   => $sourceEmail,
                    'destinataire'  => $destinataire,
                    'sujet'         => $sujet,
                    'message'       => $message,
                ];
                //envoi de l'email
                $mailService->send($sourceEmail,$destinataire,$sujet,'message',$context);

                //mise à jour de la base
                $emailEntity
                    ->setDestinataire($destinataire)
                    ->setSujet($sujet)
                    ->setMessage($message)
                    ->setCreatedAt(new \DateTime())
                    ->setUsers($this->getUser());
                $entityManager->persist($emailEntity);
                $entityManager->flush();

                $this->addFlash('message', 'Votre email a bien été envoyé.');

                return $this->redirectToRoute('app_home');
            }

            //récupération des détails de l'email à réenvoyer
            $idEmail = $request->query->getInt('idEmail');
            if($idEmail)
            {
                $emailEntity = $emailRepo->find($idEmail);
                $form = $this->createForm(EmailFormType::class,$emailEntity);
            }

            return $this->render('messagerie/send.html.twig', [
                'messagerie'    => $form->createView(),
                'titlePage'     => 'Mailer',
                'page'          => 'Nouveau message'
            ]);
        }

        //si l'utilisateur n'existe pas encore
        if(!$this->getUser())
            $this->addFlash('error', 'Veuillez vous inscrire pour accéder à l\'application.');

        return $this->redirectToRoute('app_login');
    }
}
