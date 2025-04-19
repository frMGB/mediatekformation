<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une formation.
 */
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{

    /**
     * @var string Préfixe URL pour les miniatures YouTube.
     */
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";

    /**
     * @var int|null L'identifiant unique de la formation.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var \DateTimeInterface|null La date de publication de la vidéo sur YouTube.
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

    /**
     * @var string|null Le titre de la formation/vidéo.
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    /**
     * @var string|null La description de la formation/vidéo.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var string|null L'identifiant de la vidéo YouTube.
     */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $videoId = null;

    /**
     * @var Playlist|null La playlist à laquelle cette formation appartient.
     */
    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Playlist $playlist = null;

    /**
     * @var Collection<int, Categorie> Les catégories associées à cette formation.
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    /**
     * Constructeur de l'entité Formation.
     * Initialise la collection de catégories.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la formation.
     *
     * @return int|null L'identifiant ou null s'il n'est pas défini.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date de publication.
     *
     * @return \DateTimeInterface|null La date de publication ou null.
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Définit la date de publication.
     *
     * @param \DateTimeInterface|null $publishedAt La date de publication.
     * @return static L'instance de la formation.
     */
    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Retourne la date de publication formatée en chaîne de caractères (jj/mm/aaaa).
     *
     * @return string La date formatée ou une chaîne vide si non définie.
     */
    public function getPublishedAtString(): string
    {
        if ($this->publishedAt == null) {
            return "";
        }
        return $this->publishedAt->format('d/m/Y');
    }

    /**
     * Retourne le titre de la formation.
     *
     * @return string|null Le titre ou null s'il n'est pas défini.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la formation.
     *
     * @param string|null $title Le titre à définir.
     * @return static L'instance de la formation.
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Retourne la description de la formation.
     *
     * @return string|null La description ou null si elle n'est pas définie.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la formation.
     *
     * @param string|null $description La description à définir.
     * @return static L'instance de la formation.
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne l'identifiant de la vidéo YouTube.
     *
     * @return string|null L'identifiant vidéo ou null s'il n'est pas défini.
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    /**
     * Définit l'identifiant de la vidéo YouTube.
     *
     * @param string|null $videoId L'identifiant vidéo à définir.
     * @return static L'instance de la formation.
     */
    public function setVideoId(?string $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Retourne l'URL de la miniature par défaut de la vidéo YouTube.
     *
     * @return string|null L'URL de la miniature ou null si videoId n'est pas défini.
     */
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE . $this->videoId . "/default.jpg";
    }

    /**
     * Retourne l'URL de l'image de haute qualité de la vidéo YouTube.
     *
     * @return string|null L'URL de l'image ou null si videoId n'est pas défini.
     */
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE . $this->videoId . "/hqdefault.jpg";
    }

    /**
     * Retourne la playlist associée à la formation.
     *
     * @return Playlist|null La playlist ou null si elle n'est pas définie.
     */
    public function getPlaylist(): ?playlist
    {
        return $this->playlist;
    }

    /**
     * Définit la playlist associée à la formation.
     *
     * @param Playlist|null $playlist La playlist à associer.
     * @return static L'instance de la formation.
     */
    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * Retourne la collection des catégories associées à la formation.
     *
     * @return Collection<int, Categorie> La collection des catégories.
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Ajoute une catégorie à la formation.
     *
     * @param Categorie $category La catégorie à ajouter.
     * @return static L'instance de la formation.
     */
    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * Supprime une catégorie de la formation.
     *
     * @param Categorie $category La catégorie à supprimer.
     * @return static L'instance de la formation.
     */
    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
