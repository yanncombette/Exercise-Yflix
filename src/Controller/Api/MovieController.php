<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/", name="app_api_movies_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("movies", name="list", methods="GET")
     */
    public function list(MovieRepository $movieRepository): JsonResponse
    {
        $allMovies = $movieRepository->findAll();
    
        return $this->json([
            'movies' => $allMovies,
        ], Response::HTTP_OK, [], ["groups" => "movies_list"]);
    }

    /**
     * @Route("movies", name="create", methods="POST")
     */
    public function create(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {

        $json = $request->getContent();

        $movie = $serializer->deserialize($json, Movie::class, 'json');

        $errorList = $validator->validate($movie);
        if (count($errorList) > 0)
        {
            return $this->json($errorList, Response::HTTP_BAD_REQUEST);
        }
        // dump($errorList);

        $em->persist($movie);
        $em->flush();

        return $this->json($movie, Response::HTTP_CREATED, [], ["groups" => 'movies_create']);

    }

    /**
     * @Route("movies/{id}", name="delete", methods="DELETE")
     */
    public function delete($id, EntityManagerInterface $em): JsonResponse
    {

        $movie = $em->find(Movie::class, $id);

        if ($movie === null)
        {
            $errorMessage = [
                'message' => "Movie not found",
            ];
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $em->remove($movie);

        $em->flush();

        return $this->json("Done");
    }


    /**
     * @Route("movies/random", name="random", methods="GET")
     */
    public function random(MovieRepository $movieRepository): JsonResponse
    { 
        $allMovies = $movieRepository->findAll();

        if (count($allMovies) === 0)
        {
            $errorMessage = [
                'message' => "No movies in database",
            ];
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        }

        shuffle($allMovies);
        $movie = $allMovies[0];


        $randomKey = (mt_rand(0, count($allMovies) - 1));
        $movie = $allMovies[$randomKey];

        return $this->json([
            'movie' => $movie,
        ], Response::HTTP_OK, [], ["groups" => "movies_read"]);
        
    }

    /**
     * @Route("movies/{id}", name="read", methods="GET", requirements={"id"="\d+"})
     */
    public function read($id, MovieRepository $movieRepository): JsonResponse
    {
        $movie = $movieRepository->find($id);

        if ($movie === null)
        {
            $errorMessage = [
                'message' => "Movie not found",
            ];
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'movie' => $movie,
        ], Response::HTTP_OK, [], ["groups" => "movies_read"]);
        
    }

    /**
     * @Route("movies/{id}", name="update", methods="PUT", requirements={"id"="\d+"})
     */
    public function update($id, EntityManagerInterface $em, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $movie = $em->find(Movie::class, $id);

        if ($movie === null)
        {
            $errorMessage = [
                'message' => "Movie not found",
            ];
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $json = $request->getContent();

        $serializer->deserialize($json, Movie::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $movie]);

        $errorList = $validator->validate($movie);
        if (count($errorList) > 0)
        {
            return $this->json($errorList, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->json($movie, Response::HTTP_OK, [], ["groups" => 'movies_update']);
    }
}
