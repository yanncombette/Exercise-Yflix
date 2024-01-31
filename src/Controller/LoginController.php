<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginController extends AbstractController
{
//     private $jwtManager;
//     private $security;

//     public function __construct(JWTTokenManagerInterface $jwtManager, Security $security)
//     {
//         $this->jwtManager = $jwtManager;
//         $this->security = $security;
//     }

    /**
     * @Route("/login", name="app_login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Check if the user is already authenticated
        // if ($this->isUserAuthenticated()) {
            // Generate a JWT for the authenticated user
            // $token = $this->jwtManager->create($this->getUser());

            // Return the JWT in the response
            // return new JsonResponse(['token' => $token]);
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    // /**
    //  * Check if the user is authenticated.
    //  */
    // private function isUserAuthenticated(): bool
    // {
    //     $token = $this->security->getToken();

    //     return $token && $token->getUser() instanceof UserInterface;
    // }
}
