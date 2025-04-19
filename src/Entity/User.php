<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité représentant un utilisateur de l'application.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int|null L'identifiant unique de l'utilisateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null L'adresse email de l'utilisateur, utilisée comme identifiant.
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> Les rôles attribués à l'utilisateur.
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null Le mot de passe hashé de l'utilisateur.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Retourne l'identifiant de l'utilisateur (id).
     *
     * @return int|null L'identifiant ou null s'il n'est pas défini.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'adresse email de l'utilisateur.
     *
     * @return string|null L'email ou null s'il n'est pas défini.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse email de l'utilisateur.
     *
     * @param string $email L'email à définir.
     * @return static L'instance de l'utilisateur.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retourne un indentifiant représentant l'utilisateur.
     *
     * @see UserInterface
     * @return string L'identifiant de l'utilisateur (son email).
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Retourne les rôles de l'utilisateur.
     *
     * @see UserInterface
     * @return list<string> Les rôles de l'utilisateur.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     *
     * @param list<string> $roles Les rôles à attribuer.
     * @return static L'instance de l'utilisateur.
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe de l'utilisateur.
     *
     * @see PasswordAuthenticatedUserInterface
     * @return string Le mot de passe hashé.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe hashé de l'utilisateur.
     *
     * @param string $password Le mot de passe hashé.
     * @return static L'instance de l'utilisateur.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Efface les données sensibles temporaires de l'utilisateur.
     *
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {

    }
}
