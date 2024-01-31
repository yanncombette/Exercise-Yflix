<?php

namespace App\Controller\Front;

use App\Controller\Front\BaseController;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends BaseController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(MovieRepository $movieRepository)
    {
        // Retrieve movies for the current page
        $movieList = $movieRepository->findLatestMovies(5);

        return $this->renderWithCommonData('front/main/home.html.twig', [
            'movie_list' => $movieList,
        ]);
    }


    /**
     * Page de favoris
     *
     * @Route("/favorites", name="favorites", methods={"GET"})
     */
    public function favorites(Request $request, MovieRepository $movieRepo)
    {
        $session = $request->getSession();
        $movieIdList = $session->get('favorite_movies', []);

        // dump($movieIdList);

        $movieList = $movieRepo->findBy(['id' => $movieIdList]);

        // dump($movieList);
        return $this->renderWithCommonData('front/main/favorites.html.twig', [
            'movie_list' => $movieList,
        ]);
    }
}