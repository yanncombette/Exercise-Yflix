<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\Back\UserType;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/user", name="app_back_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="browse")
     */
    public function browse(UserRepository $userRepository): Response
    {
        $userList = $userRepository->findAll();


        return $this->render('back/user/browse.html.twig', [
            'userList' => $userList,
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     */
    public function add(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $clearPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $clearPassword);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'user ajouté !');

            return $this->redirectToRoute('app_back_user_browse');

        }

        return $this->renderForm('back/user/add.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(UserPasswordHasherInterface $passwordHasher, User $user, UserRepository $userRepository, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->remove('email');

        $form->add('password', PasswordType::class, [
            'mapped' => false,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            if (!is_null($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            } else {

            }

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User modifié');

            return $this->redirectToRoute('app_back_user_browse');
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            $this->addFlash('success', 'User supprimé');
        }

        return $this->redirectToRoute('app_back_user_browse', [], Response::HTTP_SEE_OTHER);
    }

}
