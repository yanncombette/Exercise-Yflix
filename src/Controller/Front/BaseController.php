<?php

namespace App\Controller\Front;

use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    protected function renderWithCommonData(string $view, array $data = []): \Symfony\Component\HttpFoundation\Response
    {
        $genres = $this->genreRepository->findAll();

        $commonData = [
            'genres' => $genres,
        ];

        $data = array_merge($commonData, $data);

        return $this->render($view, $data);
    }
}
