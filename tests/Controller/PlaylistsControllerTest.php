<?php
// tests/Controller/PlaylistsControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Teste le contrôleur PlaylistsController (front-office).
 */
class PlaylistsControllerTest extends WebTestCase
{
    private const FIRST_PLAYLIST_NAME_SELECTOR = 'table.table tbody tr:first-child td:first-child h5';
    private const FIRST_PLAYLIST_NAME_ASC = 'Bonnes Pratiques';
    private const PLAYLISTS = '/playlists';

    /**
     * Teste le tri des playlists par nom (ASC et DESC) sur la page publique.
     */
    public function testSortByName(): void
    {
        $client = static::createClient();

        // Test ASC
        $crawler = $client->request('GET', '/playlists/tri/name/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_PLAYLIST_NAME_SELECTOR, self::FIRST_PLAYLIST_NAME_ASC);

        // Test DESC
        $crawler = $client->request('GET', '/playlists/tri/name/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_PLAYLIST_NAME_SELECTOR, 'Développement Web');
    }

    /**
     * Teste le tri des playlists par nombre de formations (ASC et DESC) sur la page publique.
     */
    public function testSortByNbFormations(): void
    {
        $client = static::createClient();

        // Test ASC
        $crawler = $client->request('GET', '/playlists/tri/nbformations/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_PLAYLIST_NAME_SELECTOR, self::FIRST_PLAYLIST_NAME_ASC);

        // Test DESC
        $crawler = $client->request('GET', '/playlists/tri/nbformations/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_PLAYLIST_NAME_SELECTOR, 'Développement Web');
    }

    // --- Nouveaux Tests pour les Filtres ---

    /**
     * Teste le filtre des playlists par nom sur la page publique.
     */
    public function testFilterByName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::PLAYLISTS);

        $form = $crawler->filter('form[action*="/playlists/recherche/name"] ')->form([
            'recherche' => 'Pratiques'
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, 'table.table tbody tr');
        $this->assertSelectorTextContains(self::FIRST_PLAYLIST_NAME_SELECTOR, self::FIRST_PLAYLIST_NAME_ASC);
    }

    /**
     * Teste le filtre des playlists par catégorie sur la page publique.
     */
    public function testFilterByCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::PLAYLISTS);

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $categorieTest = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => 'Test']);
        $this->assertNotNull($categorieTest, 'Fixture Catégorie Test non trouvée');

        $form = $crawler->filter('form[action*="/playlists/recherche/id/categories"] ')->form([
            'recherche' => $categorieTest->getId()
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, 'table.table tbody tr');
        $this->assertSelectorTextContains(self::FIRST_PLAYLIST_NAME_SELECTOR, self::FIRST_PLAYLIST_NAME_ASC);
    }

    // --- Nouveau Test pour le lien de détail ---

    /**
     * Teste le clic sur le lien de détail d'une playlist et vérifie l'affichage.
     */
    public function testClickPlaylistLink(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::PLAYLISTS);

        // Cliquer sur le lien "Voir détail" de la première playlist
        $link = $crawler->filter('table.table tbody tr:first-child td:nth-child(4) a')->link();
        $playlistName = $crawler->filter(self::FIRST_PLAYLIST_NAME_SELECTOR)->text();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', $playlistName);
    }
}
