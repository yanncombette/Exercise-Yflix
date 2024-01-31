<?php

namespace App\Controller\Back;

use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Form\Back\MovieType;
use App\Form\CastingType;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/movie", name="app_back_movie_")
 * @IsGranted("ROLE_MANAGER")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="browse")
     */
    public function browse(MovieRepository $movieRepository): Response
    {

        $movieList = $movieRepository->findAll();


        return $this->render('back/movie/browse.html.twig', [
            'movieList' => $movieList,
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     */
    public function add(MovieRepository $movieRepository, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $releaseDate = $form->get('release_date')->getData();
            $movie->setReleaseDate($releaseDate);

            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('app_back_movie_browse');

        }

        return $this->renderForm('back/movie/add.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Movie $movie, MovieRepository $movieRepository, GenreRepository $genreRepository, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $releaseDate = $form->get('release_date')->getData();
            $movie->setReleaseDate($releaseDate);

            $allGenres = $genreRepository->findAll();
            foreach ($allGenres as $currentGenre) {
                $currentGenre->removeMovie($movie);
            }
            foreach ($form->get('genres')->getData() as $currentGenre) {
                $currentGenre->addMovie($movie);
            }

            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('app_back_movie_browse');

        }

        return $this->renderForm('back/movie/edit.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete-movie-' . $movie->getId(), $request->request->get('_token'))) {
            $movieRepository->remove($movie, true);
            $this->addFlash('success', 'Film supprimé');
        }

        return $this->redirectToRoute('app_back_movie_browse', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/casting", name="casting_browse", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function castingBrowse(Movie $movie, MovieRepository $movieRepo): Response
    {

        $movieWithData = $movieRepo->findWithAssociatedData($movie->getId());

        return $this->render('back/movie/casting/browse.html.twig', [
            'movie' => $movieWithData
        ]);
    }

    /**
     * @Route("/{id}/casting/add", name="casting_add", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function castingAdd(EntityManagerInterface $em, Movie $movie, Request $request): Response
    {
        $casting = new Casting();

        $form = $this->createForm(CastingType::class, $casting);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $creditOrder = 0;
            foreach ($movie->getCastings() as $currentCasting) {
                if ($currentCasting->getCreditOrder() > $creditOrder) {
                    $creditOrder = $currentCasting->getCreditOrder();
                }
            }
            $creditOrder += 1;

            $casting->setCreditOrder($creditOrder);
            $casting->setMovie($movie);

            $em->persist($casting);
            $em->flush();

            $this->addFlash('success', 'casting ajouté');

            return $this->redirectToRoute('app_back_movie_casting_browse', ['id' => $movie->getId()]);
        }

        return $this->renderForm('back/movie/casting/add.html.twig', [
            'form' => $form,
            'movie' => $movie
        ]);
    }
}
