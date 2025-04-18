<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class FormationRepositoryTest extends KernelTestCase
{
    private const FORMATION_TITLE_SYMFONY_AVANCE = 'Symfony Avancé : Services et Injection';
    private const FORMATION_TITLE_FONDAMENTAUX_PHP = 'Les fondamentaux de PHP 8';
    private const PLAYLIST_NAME_DEVELOPPEMENT_WEB = 'Développement Web';

    // --- Tests pour findAllOrderBy ---

    public function testFindAllOrderByDateDesc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findAllOrderBy('publishedAt', 'DESC');
        $this->assertCount(5, $formations);
        $this->assertEquals(self::FORMATION_TITLE_SYMFONY_AVANCE, $formations[0]->getTitle());
        $this->assertEquals(self::FORMATION_TITLE_FONDAMENTAUX_PHP, $formations[4]->getTitle());
        $previousDate = $formations[0]->getPublishedAt();
        for ($i = 1; $i < count($formations); $i++) {
            $currentDate = $formations[$i]->getPublishedAt();
            $this->assertLessThanOrEqual($previousDate->getTimestamp(), $currentDate->getTimestamp());
            $previousDate = $currentDate;
        }
    }

    public function testFindAllOrderByDateAsc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findAllOrderBy('publishedAt', 'ASC');
        $this->assertCount(5, $formations);
        $this->assertEquals(self::FORMATION_TITLE_FONDAMENTAUX_PHP, $formations[0]->getTitle());
        $this->assertEquals(self::FORMATION_TITLE_SYMFONY_AVANCE, $formations[4]->getTitle());
        $previousDate = $formations[0]->getPublishedAt();
        for ($i = 1; $i < count($formations); $i++) {
            $currentDate = $formations[$i]->getPublishedAt();
            $this->assertGreaterThanOrEqual($previousDate->getTimestamp(), $currentDate->getTimestamp());
            $previousDate = $currentDate;
        }
    }

    public function testFindAllOrderByTitleAsc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findAllOrderBy('title', 'ASC');
        $this->assertCount(5, $formations);
        $this->assertEquals('Bases HTML5 et CSS3', $formations[0]->getTitle());
        $this->assertEquals('Tests Unitaires en PHP avec PHPUnit', $formations[4]->getTitle());
    }

    public function testFindAllOrderByPlaylistNameDesc(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findAllOrderBy('name', 'DESC', 'playlist');
        $this->assertCount(5, $formations);
        $this->assertEquals(self::PLAYLIST_NAME_DEVELOPPEMENT_WEB, $formations[0]->getPlaylist()->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DEVELOPPEMENT_WEB, $formations[1]->getPlaylist()->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DEVELOPPEMENT_WEB, $formations[2]->getPlaylist()->getName());
        $this->assertEquals('Bonnes Pratiques', $formations[3]->getPlaylist()->getName());
        $this->assertEquals('Bonnes Pratiques', $formations[4]->getPlaylist()->getName());
    }

    // --- Tests pour findByContainValue ---

    public function testFindByContainValueTitleFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findByContainValue('title', 'Symfony');
        $this->assertCount(2, $formations);
        $this->assertStringContainsString('Symfony', $formations[0]->getTitle());
        $this->assertStringContainsString('Symfony', $formations[1]->getTitle());
    }

    public function testFindByContainValueTitleNotFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findByContainValue('title', 'JavaZZZ');
        $this->assertCount(0, $formations);
    }

    public function testFindByContainValueDescriptionFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findByContainValue('description', 'bases');
        $this->assertCount(2, $formations);
        $this->assertStringContainsString('bases', $formations[0]->getDescription());
        $this->assertStringContainsString('bases', $formations[1]->getDescription());
    }

    public function testFindByContainValuePlaylistNameFound(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findByContainValue('name', 'Web', 'playlist');
        $this->assertCount(3, $formations);
        $this->assertEquals(self::PLAYLIST_NAME_DEVELOPPEMENT_WEB, $formations[0]->getPlaylist()->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DEVELOPPEMENT_WEB, $formations[1]->getPlaylist()->getName());
        $this->assertEquals(self::PLAYLIST_NAME_DEVELOPPEMENT_WEB, $formations[2]->getPlaylist()->getName());
    }

    public function testFindByContainValueEmptyValue(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findByContainValue('title', '');
        $this->assertCount(5, $formations);
    }

    // --- Test pour findAllLasted ---

    public function testFindAllLasted(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $formations = $repository->findAllLasted(2);
        $this->assertCount(2, $formations);
        $this->assertEquals(self::FORMATION_TITLE_SYMFONY_AVANCE, $formations[0]->getTitle());
        $this->assertEquals('Introduction à Symfony 6', $formations[1]->getTitle());
    }

    // --- Test pour findAllForOnePlaylist ---

    public function testFindAllForOnePlaylist(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Formation::class);
        $playlistRepository = $entityManager->getRepository(Playlist::class);
        $playlistDevWeb = $playlistRepository->findOneBy(['name' => self::PLAYLIST_NAME_DEVELOPPEMENT_WEB]);
        $this->assertNotNull($playlistDevWeb, 'La playlist Développement Web devrait exister');
        $formations = $repository->findAllForOnePlaylist($playlistDevWeb->getId());
        $this->assertCount(3, $formations);
        $this->assertEquals(self::FORMATION_TITLE_FONDAMENTAUX_PHP, $formations[0]->getTitle());
        $this->assertEquals('Bases HTML5 et CSS3', $formations[1]->getTitle());
        $this->assertEquals('Introduction à Symfony 6', $formations[2]->getTitle());
    }
}
