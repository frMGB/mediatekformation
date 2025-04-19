<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'authentification (connexion et déconnexion).
 */
class SecurityController extends AbstractController
{
    /**
     * Gère l'affichage du formulaire de connexion et les erreurs d'authentification.
     *
     * @param AuthenticationUtils $authenticationUtils Utilitaires pour l'authentification.
     * @return Response La réponse HTTP (affichage du formulaire ou redirection).
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirection vers l'admin
        if ($this->getUser()) {
            return $this->redirectToRoute('admin.formations.index');
        }

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     * Cette méthode peut être vide car elle est interceptée par le pare-feu de sécurité.
     *
     * @throws \LogicException Ne devrait jamais être appelée directement.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
