<?php

namespace App\DataFixtures;

use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Season;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixturesPHP extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\TvShow($faker));

        mt_srand(42);
        $genreNames = [];
        $genreNames[] = 'Action';
        $genreNames[] = 'Animation';
        $genreNames[] = 'Aventure';
        $genreNames[] = 'Comédie';
        $genreNames[] = 'Dessin animé';
        $genreNames[] = 'Documentaire';
        $genreNames[] = 'Drame';
        $genreNames[] = 'Espionnage';
        $genreNames[] = 'Famille';
        $genreNames[] = 'Fantastique';
        $genreNames[] = 'Historique';
        $genreNames[] = 'Policier';
        $genreNames[] = 'Romance';
        $genreNames[] = 'Science-fiction';
        $genreNames[] = 'Thriller';
        $genreNames[] = 'Western';

        $genreObjectList = [];
        foreach ($genreNames as $currentGenreName) {
            $genre = new Genre();
            $genre->setName($currentGenreName);

            $manager->persist($genre);
            $genreObjectList[] = $genre;
        }

        $actorList = [
            [
                'firstName' => 'Grégory',
                'lastName' => 'Peck',
            ],
        ];
        $personObjectList = [];
        for ($nbPersonToAdd = 1; $nbPersonToAdd <= 500; $nbPersonToAdd++) {
            $person = new Person();
            $person->setFirstname($faker->firstname());
            $person->setLastname($faker->lastname());

            $manager->persist($person);
            $personObjectList[] = $person;
        }

        /* Création des Movies */
        $movieType = ['Série', 'Film'];

        for ($numeroMovie = 1; $numeroMovie <= 200; $numeroMovie++) {
            $movie = new Movie();


            $posterUrl = "https://picsum.photos/id/" . mt_rand(0, 1084) . "/200/300";
            $movie->setPoster($posterUrl);
            $movie->setDuration(mt_rand(45, 180));
            $movie->setRating($faker->randomFloat(1, 0, 5));
            $movie->setTitle($faker->movie());
            $movie->setType($movieType[mt_rand(0, 1)]);
            $movie->setSynopsis($faker->realTextBetween(160, 200));
            $movie->setSummary($faker->realText(50));
            $movie->setReleaseDate($faker->dateTimeThisCentury());

            $nbGenreToAdd = mt_rand(1, 4);
            $faker->unique(true)->randomElement($genreObjectList);
            for (; $nbGenreToAdd > 0; $nbGenreToAdd--) {


                $randomGenre = $faker->randomElement($genreObjectList);
                $movie->addGenre($randomGenre);
            }

            if ($movie->getType() === 'Série') {

                $movie->setTitle($faker->tvShow());

                $nbSeasonToAdd = mt_rand(1, 8);
                for (; $nbSeasonToAdd > 0; $nbSeasonToAdd--) {
                    $season = new Season();
                    $season->setEpisodesNumber(mt_rand(3, 10));
                    $season->setNumber($nbSeasonToAdd);
                    $season->setMovie($movie);

                    $manager->persist($season);
                }
            }

            $nbCastingToAdd = mt_rand(5, 25);
            $faker->unique(true)->randomElement($personObjectList);
            for (; $nbCastingToAdd > 0; $nbCastingToAdd--) {
                $casting = new Casting();
                $casting->setMovie($movie);
                $casting->setRole($faker->name());
                $casting->setCreditOrder($nbCastingToAdd);

                $randomPerson = $faker->unique()->randomElement($personObjectList);
                $casting->setPerson($randomPerson);

                $manager->persist($casting);
            }

            $manager->persist($movie);
        }

        $manager->flush();
    }
}
