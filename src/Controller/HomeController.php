<?php

namespace App\Controller;

use App\Repository\EmailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(EmailsRepository $emailRepo): Response
    {
        //si l'utilisateur existe et qu'il a vérifié son compte
        if($this->getUser() && $this->getUser()->isVerified())
        {
            return $this->render('home/index.html.twig', [
                //récupération de tous les emails envoyés de l'utilisateur
                'emails'            => $emailRepo->findBy(['users' => $this->getUser()], ['created_at' => 'desc']),
                'titlePage'         => 'Mailer',
                'page'              => 'Liste des emails'
            ]);
        } 
        
        //si l'utilisateur n'existe pas encore
        if(!$this->getUser())
            $this->addFlash('error', 'Veuillez vous inscrire pour accéder à l\'application.');

        return $this->redirectToRoute('app_login');
    }
}
