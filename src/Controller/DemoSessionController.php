<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DemoSessionController extends AbstractController
{
    /**
     * @Route("/demo/session", name="app_demo_session")
     */
    public function index(Request $request)
    {
        $session = $request->getSession();

        $username = $session->get('username');
        // dump($session);
        // dump($username);

        return $this->render('demo_session/index.html.twig', [
            'username' => $username,
        ]);
    }

    /**
     * Create session element => ajoute $name dans la session
     *
     * @Route("/demo/session/add/{name}", name="app_demo_session_add")
     */
    public function add(Request $request, $name)
    {
        $session = $request->getSession();

        $session->set('username', $name);
        return $this->redirectToRoute('app_demo_session');
    }
}
