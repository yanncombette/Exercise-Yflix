<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 100)
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $release_date;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $duration;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, mappedBy="movies")
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="movie", orphanRemoval=true)
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $seasons;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=200)
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $summary;

    /**
     * @ORM\Column(type="text")
     * @Groups({"movies_list", "movies_read", "movies_create", "movies_create"})
     */
    private $synopsis;

    /**
     * @ORM\Column(type="string", length=2083, nullable=true)
     * @Assert\Url
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $poster;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 5)
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="movie", orphanRemoval=true)
     * @Groups({"movies_list", "movies_read", "movies_create"})
     */
    private $castings;
    // * @ORM\OrderBy({"creditOrder" = "ASC"})

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="movie", orphanRemoval=true)
     */
    private $reviews;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->castings = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(?\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
            $genre->addMovie($this);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->removeElement($genre)) {
            $genre->removeMovie($this);
        }

        return $this;
    }

    public function removeAllGenre(): self
    {
        foreach ($this->genres as $currentGenre) {
            $currentGenre->removeMovie($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setMovie($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getMovie() === $this) {
                $season->setMovie(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Casting>
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->removeElement($casting)) {
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of reviews
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set the value of reviews
     *
     * @return  self
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;

        return $this;
    }

}
