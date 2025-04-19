<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Playlist.
 *
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre de services.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Persiste et flush une entité Playlist.
     *
     * @param Playlist $entity L'entité à persister.
     */
    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime et flush une entité Playlist.
     *
     * @param Playlist $entity L'entité à supprimer.
     */
    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les playlists triées par nom.
     *
     * @param string $ordre L'ordre de tri ('ASC' ou 'DESC').
     * @return Playlist[] Liste des playlists triées par nom.
     */
    public function findAllOrderByName($ordre): array
    {
        return $this->createQueryBuilder('p')
            ->leftjoin('p.formations', 'f')
            ->groupBy('p.id')
            ->orderBy('p.name', $ordre)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les playlists dont le nom ou le nom d'une catégorie associée contient une valeur.
     * Si la valeur est vide, retourne toutes les playlists triées par nom ASC.
     *
     * @param string $champ Le champ à rechercher (`name` pour la playlist, `name` pour la catégorie).
     * @param string $valeur La valeur à rechercher.
     * @param string $table Table liée pour la recherche.
     * @return Playlist[] Liste des playlists filtrées et triées par nom ASC.
     */
    public function findByContainValue($champ, $valeur, $table = ""): array
    {
        if ($valeur == "") {
            return $this->findAllOrderByName('ASC');
        }
        if ($table == "") {
            return $this->createQueryBuilder('p')
                ->leftjoin('p.formations', 'f')
                ->where("p.{$champ} LIKE :valeur")
                ->setParameter('valeur', "%{$valeur}%")
                ->groupBy('p.id')
                ->orderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('p')
                ->leftjoin('p.formations', 'f')
                ->leftjoin('f.categories', 'c')
                ->where("c.{$champ} LIKE :valeur")
                ->setParameter('valeur', "%{$valeur}%")
                ->groupBy('p.id')
                ->orderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Retourne toutes les playlists triées par nombre de formations ascendant.
     *
     * @return Playlist[] Liste des playlists triées par nombre de formations (ASC).
     */
    public function findAllOrderByNbFormationsASC(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->groupBy('p.id')
            ->orderBy('COUNT(f.id)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne toutes les playlists triées par nombre de formations descendant.
     *
     * @return Playlist[] Liste des playlists triées par nombre de formations (DESC).
     */
    public function findAllOrderByNbFormationsDESC(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->groupBy('p.id')
            ->orderBy('COUNT(f.id)', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
