<?php

namespace App\Utility;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaginationGenerator extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('generate_pagination', [$this, 'generatePagination']),
        ];
    }

    public function generatePagination(int $current_page, int $total_pages): array
    {
        $pagination = [];

        // Calculate start and end pages
        $startPage = max(1, $current_page - 1);
        $endPage = min($total_pages, $startPage + 3);

        // Add "Previous" link
        $previousPage = max(1, $current_page - 1);
        $pagination[] = [
            'label' => '«',
            'url' => $previousPage == $current_page ? '#' : '?page=' . $previousPage,
            'class' => $current_page == 1 ? 'page-item disabled' : 'page-item',
        ];

        // Add page links
        for ($page = $startPage; $page <= $endPage; $page++) {
            $pagination[] = [
                'label' => $page,
                'url' => '?page=' . $page,
                'class' => $current_page == $page ? 'page-item active' : 'page-item',
            ];
        }

        // Add "Next" link
        $nextPage = min($total_pages, $current_page + 1);
        $pagination[] = [
            'label' => '»',
            'url' => $nextPage > $current_page ? '?page=' . $nextPage : '#',
            'class' => $current_page == $total_pages ? 'page-item disabled' : 'page-item',
        ];

        return $pagination;
    }
}
