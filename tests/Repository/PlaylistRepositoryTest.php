<?php

namespace App\Tests\Repository;

use App\Entity\Categorie; // Ajout pour référence
use App\Entity\Formation; // Ajout pour référence
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

/**
 * Teste le repository PlaylistRepository.
 */
class PlaylistRepositoryTest extends KernelTestCase
{
    private const PLAYLIST_NAME_ASC = 'Bonnes Pratiques';
    private const PLAYLIST_NAME_DESC = 'Développement Web';

    // --- Tests pour findAllOrderByName ---

    /**
     * Teste le tri des playlists par nom ASC.
     */
    public function testFindAllOrderByNameAsc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findAllOrderByName('ASC');
        $this->assertCount(2, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_ASC, $playlists[0]->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DESC, $playlists[1]->getName());
    }

    /**
     * Teste le tri des playlists par nom DESC.
     */
    public function testFindAllOrderByNameDesc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findAllOrderByName('DESC');
        $this->assertCount(2, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_DESC, $playlists[0]->getName());
        $this->assertEquals(self::PLAYLIST_NAME_ASC, $playlists[1]->getName());
    }

    // --- Tests pour findByContainValue ---

    /**
     * Teste la recherche par nom de playlist avec un résultat trouvé.
     */
    public function testFindByContainValueNameFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findByContainValue('name', 'Web');
        $this->assertCount(1, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_DESC, $playlists[0]->getName());
    }

    /**
     * Teste la recherche par nom de playlist avec aucun résultat.
     */
    public function testFindByContainValueNameNotFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findByContainValue('name', 'MobileZZZ');
        $this->assertCount(0, $playlists);
    }

    /**
     * Teste la recherche par nom de catégorie avec des résultats trouvés (cas général).
     */
    public function testFindByContainValueCategoryNameFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findByContainValue('name', 'Symfony', 'categories');
        $this->assertCount(2, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_ASC, $playlists[0]->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DESC, $playlists[1]->getName());
    }

    /**
     * Teste la recherche par nom de catégorie avec un résultat trouvé (cas spécifique).
     */
    public function testFindByContainValueCategoryNameSpecificFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findByContainValue('name', 'Test', 'categories');
        $this->assertCount(1, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_ASC, $playlists[0]->getName());
    }

    /**
     * Teste la recherche par nom de catégorie avec aucun résultat.
     */
    public function testFindByContainValueCategoryNameNotFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findByContainValue('name', 'JavaZZZ', 'categories');
        $this->assertCount(0, $playlists);
    }

    /**
     * Teste la recherche avec une valeur vide, doit retourner tous les éléments.
     */
    public function testFindByContainValueEmptyValue(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findByContainValue('name', '');
        $this->assertCount(2, $playlists);
    }

    // --- Tests pour findAllOrderByNbFormations ---

    /**
     * Teste le tri des playlists par nombre de formations ASC.
     */
    public function testFindAllOrderByNbFormationsAsc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findAllOrderByNbFormationsASC();
        $this->assertCount(2, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_ASC, $playlists[0]->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DESC, $playlists[1]->getName());
    }

    /**
     * Teste le tri des playlists par nombre de formations DESC.
     */
    public function testFindAllOrderByNbFormationsDesc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Playlist::class);
        $playlists = $repository->findAllOrderByNbFormationsDESC();
        $this->assertCount(2, $playlists);
        $this->assertEquals(self::PLAYLIST_NAME_DESC, $playlists[0]->getName());
        $this->assertEquals(self::PLAYLIST_NAME_ASC, $playlists[1]->getName());
    }
}
