<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authentificateur pour le formulaire de connexion principal de l'application.
 */
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /**
     * @var string Nom de la route utilisée pour la connexion.
     */
    public const LOGIN_ROUTE = 'app_login';

    /**
     * Constructeur de l'authentificateur.
     *
     * @param UrlGeneratorInterface $urlGenerator Générateur d'URL pour les redirections.
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Crée un "passeport" d'authentification basé sur les données de la requête.
     *
     * @param Request $request La requête HTTP contenant les identifiants.
     * @return Passport Le passeport d'authentification.
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Définit la redirection après une authentification réussie.
     * Redirige vers la page cible si elle existe, sinon vers la page d'administration des formations.
     *
     * @param Request $request La requête HTTP.
     * @param TokenInterface $token Le token d'authentification.
     * @param string $firewallName Le nom du pare-feu.
     * @return Response|null La réponse de redirection ou null.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirection par défaut après la connexion réussie
        return new RedirectResponse($this->urlGenerator->generate('admin.formations.index'));
    }

    /**
     * Retourne l'URL de la page de connexion.
     *
     * @param Request $request La requête HTTP.
     * @return string L'URL de connexion.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
