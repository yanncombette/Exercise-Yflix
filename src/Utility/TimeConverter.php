<?php

namespace App\Utility;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeConverter extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('convertMovieDuration', [$this, 'convert']),
        ];
    }

    public function convert(float $timeInMinutes)
    {
        $result = '';

        $minutesLeft = $timeInMinutes;

        $hours = floor($minutesLeft / 60);
        $minutesLeft = $minutesLeft - 60 * $hours;

        $minutes = floor($minutesLeft);
        $minutesLeft = $minutesLeft - $minutes;

        $seconds = $minutesLeft * 60;

        if ($hours > 0) {
            $result .= ' ' . $hours . 'h';
        }

        if ($minutes > 0) {
            $result .= ' ' . $minutes . 'min';
        }

        if ($seconds > 0) {
            $result .= ' ' . $seconds . 's';
        }

        return trim($result);
    }

}
