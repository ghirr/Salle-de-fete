<?php

// src/Controller/AdminController.php

// src/Controller/AdminController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, SessionInterface $session): Response
    {
        $error = null;

        // Vérification des identifiants
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        if ($username === 'admin' && $password === 'admin') {
            // Les identifiants sont valides, créer la variable de session "admin"
            $session->set('admin', true);

            // Redirection vers la page "dash"
            return $this->redirectToRoute('app_dash');
        } else {
            // Les identifiants sont incorrects, afficher un message d'erreur
            $error = 'Invalid credentials';
        }

        // Afficher le formulaire de connexion avec l'erreur
        return $this->render('admin/login.html.twig', [
            'error' => $error,
            'admin' => $session->get('admin') // Passer la variable de session "admin" au template
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('admin');

        return $this->redirectToRoute('accueil');
    }
}
