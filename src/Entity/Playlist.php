<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une playlist.
 */
#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    /**
     * @var int|null L'identifiant unique de la playlist.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Le nom de la playlist.
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    /**
     * @var string|null La description de la playlist.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Formation> Les formations incluses dans cette playlist.
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'playlist')]
    private Collection $formations;

    /**
     * Constructeur de l'entité Playlist.
     * Initialise la collection de formations.
     */
    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la playlist.
     *
     * @return int|null L'identifiant ou null s'il n'est pas défini.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de la playlist.
     *
     * @return string|null Le nom ou null s'il n'est pas défini.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la playlist.
     *
     * @param string|null $name Le nom à définir.
     * @return static L'instance de la playlist.
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne la description de la playlist.
     *
     * @return string|null La description ou null si elle n'est pas définie.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la playlist.
     *
     * @param string|null $description La description à définir.
     * @return static L'instance de la playlist.
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne la collection des formations associées à la playlist.
     *
     * @return Collection<int, Formation> La collection des formations.
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    /**
     * Ajoute une formation à la playlist.
     *
     * @param Formation $formation La formation à ajouter.
     * @return static L'instance de la playlist.
     */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }

        return $this;
    }

    /**
     * Supprime une formation de la playlist.
     *
     * @param Formation $formation La formation à supprimer.
     * @return static L'instance de la playlist.
     */
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation) && $formation->getPlaylist() === $this) {
            $formation->setPlaylist(null);
        }

        return $this;
    }

    /**
     * Retourne une collection contenant les noms des catégories
     * présentes dans les formations de cette playlist.
     *
     * @return Collection<int, string> Collection des noms de catégories.
     */
    public function getCategoriesPlaylist(): Collection
    {
        $categories = new ArrayCollection();
        foreach ($this->formations as $formation) {
            $categoriesFormation = $formation->getCategories();
            foreach ($categoriesFormation as $categorieFormation) {
                if (!$categories->contains($categorieFormation->getName())) {
                    $categories[] = $categorieFormation->getName();
                }
            }
        }
        return $categories;
    }

    /**
     * Retourne le nombre total de formations dans la playlist.
     *
     * @return int Le nombre de formations.
     */
    public function getNbFormations(): int
    {
        return $this->formations->count();
    }
}
