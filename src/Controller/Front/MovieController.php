<?php

namespace App\Controller\Front;

use App\Controller\Front\BaseController;
use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\MovieRepository;
use App\Utility\TimeConverter;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 *
 * @Route("/movie", name="movie_")
 */
class MovieController extends BaseController
{
    /**
     * Page de liste
     *
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(MovieRepository $movieRepository, Request $request)
    {
        // Calculate total number of movies and pages
        $totalMovies = $movieRepository->count([]);
        $moviesPerPage = 3;
        $totalPages = ceil($totalMovies / $moviesPerPage);

        // Get current page from the request
        $pageNumber = $request->query->get('page', 1);


        // Calculate offset based on the current page
        $offSet = ($pageNumber - 1) * $moviesPerPage;

        // Check if a search query is present in the request
        $searchQuery = $request->query->get('search');
        $genre = $request->query->get('genre');

        if ($searchQuery) {
            // If a search query is present, filter movies based on the query
            $movieList = $movieRepository->searchMovies($searchQuery);

            // Check if the search returned no results
            if (empty($movieList)) {
                $this->addFlash('warning', 'No results found for your search.');
            }
        } elseif ($genre) {
            // If a genre is present, filter movies based on the genre
            $movieList = $movieRepository->findByGenre($genre);
        } else {
            // Retrieve movies for the current page if no search query or genre
            $movieList = $movieRepository->findBy([], ['id' => 'DESC'], $moviesPerPage, $offSet);
        }
    

        return $this->renderWithCommonData('front/movie/list.html.twig', [
            'movie_list' => $movieList,
            'current_page' => $pageNumber,
            'total_pages' => $totalPages,
        ]);
    }

    /**
     * Page de détail d'un film
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(TimeConverter $timeConverter, int $id, MovieRepository $movieRepository, ReviewRepository $reviewRepository)
    {

        $movie = $movieRepository->findWithAssociatedData($id);

        if ($movie === null) {


            throw $this->createNotFoundException('Film non trouvé !');
        }

        $movieTime = $timeConverter->convert($movie->getDuration());
        $reviewList = $reviewRepository->findBy(['movie' => $movie]);
        return $this->renderWithCommonData('front/movie/show.html.twig', [
            'movie' => $movie,
            'reviewList' => $reviewList,
            'calculated_duration' => $movieTime,
        ]);
    }

    /**
     * Page de détail d'un film
     *
     * @Route("/{id}/review/add", name="review_add", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function addReview(Movie $movie, Request $request, EntityManagerInterface $entityManager)
    {
        $review = new Review();

        $review->setMovie($movie);
        // $review->setWatchedAt(new DateTimeImmutable());

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $watchedAt = $form->get('watchedAt')->getData();
            $review->setWatchedAt($watchedAt);

            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté');

            return $this->redirectToRoute('movie_show', ["id" => $movie->getId()]);
        }

        return $this->renderForm('front/movie/review_add.html.twig', [
            'reviewForm' => $form,
            'movie' => $movie,
        ]);
    }

    /**
     * Toggle favorite asynchronously
     *
     * @Route("/{id}/favorite/toggle", name="favorite_toggle", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function toggleFavorite(Movie $movie, Request $request, FlashBagInterface $flashBag)
    {

        $session = $request->getSession();
        $favoriteMovies = $session->get('favorite_movies', []);

        if (isset($favoriteMovies[$movie->getId()])) {
            // Movie is already in favorites, remove it
            unset($favoriteMovies[$movie->getId()]);
            $message = 'Film retiré des favoris';
        } else {
            // Movie is not in favorites, add it
            $favoriteMovies[$movie->getId()] = $movie->getId();
            $message = 'Film ajouté aux favoris';
        }

        $session->set('favorite_movies', $favoriteMovies);

        // Add a flash message for additional user feedback
        $flashBag->add('success', $message);
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }

    /**
     * Remove all favorite movies
     *
     * @Route("/favorite/remove-all", name="remove_all_favorites", methods={"GET"})
     */
    public function removeAllFavorites(Request $request, FlashBagInterface $flashBag)
    {
        $session = $request->getSession();
        $session->set('favorite_movies', []);

        // Add a flash message for additional user feedback
        $flashBag->add('success', 'Tous les films ont été retirés des favoris');

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

}