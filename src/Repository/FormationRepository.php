<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Formation.
 *
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre de services.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * Persiste et flush une entité Formation.
     *
     * @param Formation $entity L'entité à persister.
     */
    public function add(Formation $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime et flush une entité Formation.
     *
     * @param Formation $entity L'entité à supprimer.
     */
    public function remove(Formation $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les formations triées sur un champ spécifique.
     *
     * @param string $champ Le champ de l'entité Formation ou d'une entité liée sur lequel trier.
     * @param string $ordre L'ordre de tri ('ASC' ou 'DESC').
     * @param string $table Le nom de la propriété de la relation si le champ appartient à une table liée.
     * @return Formation[] Liste des formations triées.
     */
    public function findAllOrderBy($champ, $ordre, $table = ""): array
    {
        if ($table == "") {
            return $this->createQueryBuilder('f')
                ->orderBy("f.{$champ}", $ordre)
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('f')
                ->join("f.{$table}", 't')
                ->orderBy("t.{$champ}", $ordre)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Retourne les formations dont un champ contient une certaine valeur.
     * Si la valeur est vide, retourne toutes les formations.
     *
     * @param string $champ Le champ de l'entité Formation ou d'une entité liée dans lequel chercher.
     * @param string $valeur La valeur à rechercher (partielle).
     * @param string $table Le nom de la propriété de la relation si le champ appartient à une table liée.
     * @return Formation[] Liste des formations correspondant à la recherche, triées par date de publication descendante.
     */
    public function findByContainValue($champ, $valeur, $table = ""): array
    {
        if ($valeur == "") {
            return $this->findAll();
        }
        if ($table == "") {
            return $this->createQueryBuilder('f')
                ->where("f.{$champ} LIKE :valeur")
                ->orderBy('f.publishedAt', 'DESC')
                ->setParameter('valeur', "%{$valeur}%")
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('f')
                ->join("f.{$table}", 't')
                ->where("t.{$champ} LIKE :valeur")
                ->orderBy('f.publishedAt', 'DESC')
                ->setParameter('valeur', "%{$valeur}%")
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Retourne les N formations les plus récentes.
     *
     * @param int $nb Le nombre de formations à retourner.
     * @return Formation[] Liste des N formations les plus récentes.
     */
    public function findAllLasted($nb): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.publishedAt', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la liste des formations appartenant à une playlist donnée, triées par date de publication ascendante.
     *
     * @param int $idPlaylist L'identifiant de la playlist.
     * @return Formation[] Liste des formations de la playlist.
     */
    public function findAllForOnePlaylist($idPlaylist): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.playlist', 'p')
            ->where('p.id=:id')
            ->setParameter('id', $idPlaylist)
            ->orderBy('f.publishedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
