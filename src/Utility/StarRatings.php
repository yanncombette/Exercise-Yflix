<?php

namespace App\Utility;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StarRatings extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('star_rating', [$this, 'starRatingFilter']),
        ];
    }

    public function starRatingFilter($rating)
    {
        $fullStars = floor($rating);
        $decimalPart = $rating - $fullStars;

        $stars = [];

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $stars[] = 'bi-star-fill';
            } elseif ($i == $fullStars + 1 && $decimalPart > 0 && $decimalPart < 0.9) {
                $stars[] = 'bi-star-half';
            } else {
                $stars[] = 'bi-star';
            }
        }

        return $stars;
    }
}
