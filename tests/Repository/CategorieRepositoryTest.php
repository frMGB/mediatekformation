<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Playlist;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class CategorieRepositoryTest extends KernelTestCase
{
    public function testFindAllForOnePlaylist(): void
    {
        // Récupérer l'entity manager directement
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Categorie::class);
        $playlistRepository = $entityManager->getRepository(Playlist::class);

        // Charger les fixtures et exécuter le test sur une base de données transactionnelle propre.

        $playlistDevWeb = $playlistRepository->findOneBy(['name' => 'Développement Web']);
        $this->assertNotNull($playlistDevWeb, 'La playlist Développement Web devrait exister');

        $categories = $repository->findAllForOnePlaylist($playlistDevWeb->getId());

        $this->assertCount(2, $categories);
        $categoryNames = array_map(fn(Categorie $c) => $c->getName(), $categories);
        $this->assertContains('PHP', $categoryNames);
        $this->assertContains('Symfony', $categoryNames);
        $this->assertEquals('PHP', $categories[0]->getName());
        $this->assertEquals('Symfony', $categories[1]->getName());
    }

    public function testFindAllForOnePlaylistBonnesPratiques(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(Categorie::class);
        $playlistRepository = $entityManager->getRepository(Playlist::class);

        $playlistBonnesPratiques = $playlistRepository->findOneBy(['name' => 'Bonnes Pratiques']);
        $this->assertNotNull($playlistBonnesPratiques, 'La playlist Bonnes Pratiques devrait exister');

        $categories = $repository->findAllForOnePlaylist($playlistBonnesPratiques->getId());

        $this->assertCount(3, $categories);
        $categoryNames = array_map(fn(Categorie $c) => $c->getName(), $categories);
        sort($categoryNames);
        $this->assertEquals(['PHP', 'Symfony', 'Test'], $categoryNames);
    }
}
