<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function add(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Movie[] Returns an array of Movie objects
     */
    public function findOrderedByTitle($limit = 2): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
        FROM App\Entity\Movie m
        ORDER BY m.duration DESC'
        )->setMaxResults($limit);

        return $query->getResult();
    }

    public function findWithAssociatedData($movieId): ?Movie
    {

        $entityManager = $this->getEntityManager();


        $query = $entityManager->createQuery(
            'SELECT m, g, c, p, s
        FROM App\Entity\Movie m
        LEFT JOIN m.genres g
        LEFT JOIN m.castings c
        LEFT JOIN c.person p
        LEFT JOIN m.seasons s
        WHERE m.id = :id
        '
        );
        $query->setParameter('id', $movieId);

        return $query->getOneOrNullResult();
    }

    public function findLatestMovies(int $limit = 5): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchMovies($query)
    {
        return $this->createQueryBuilder('m')
            ->where('m.title LIKE :query OR m.summary LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('CASE WHEN m.title LIKE :query THEN 1 ELSE 2 END, m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByGenre(string $genre)
    {
        return $this->createQueryBuilder('m')
            ->join('m.genres', 'g') 
            ->andWhere('g.name = :genre') 
            ->setParameter('genre', $genre)
            ->getQuery()
            ->getResult();
    }
    
}
