<?php

namespace App\Controller;

use App\Model\Movies;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * Get movies collection
     *
     * @Route("/demo/api/movies", name="api_movies_get", methods={"GET"})
     */
    public function moviesGet()
    {
        $movies = Movies::getMovies();
        return $this->json(['movies' => $movies]);
    }
}