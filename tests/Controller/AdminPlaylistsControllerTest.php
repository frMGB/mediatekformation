<?php
// tests/Controller/AdminPlaylistsControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;

class AdminPlaylistsControllerTest extends WebTestCase
{
    private const ADMIN_PLAYLISTS = '/admin/playlists';
    private const TABLE_FIRST_ROW_FIRST_CELL_SELECTOR = 'table.table tbody tr:first-child td:first-child';
    private const PLAYLIST_NAME_BONNES_PRATIQUES = 'Bonnes Pratiques';

    // Méthode helper pour connecter l'admin
    private function createAuthenticatedClient()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $adminUser = $userRepository->findOneBy(['email' => 'admin@test.com']);
        if (!$adminUser) {
            $this->markTestSkipped('Utilisateur admin non trouvé pour les tests.');
        }
        $client->loginUser($adminUser);
        return $client;
    }

    // Test tri par nom de playlist
    public function testAdminSortByName(): void
    {
        $client = $this->createAuthenticatedClient();

        // Test ASC
        $crawler = $client->request('GET', '/admin/playlists/tri/name/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR, self::PLAYLIST_NAME_BONNES_PRATIQUES);

        // Test DESC
        $crawler = $client->request('GET', '/admin/playlists/tri/name/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR, 'Développement Web');
    }

    // Test tri par nombre de formations
    public function testAdminSortByNbFormations(): void
    {
        $client = $this->createAuthenticatedClient();

        // Test ASC
        $crawler = $client->request('GET', '/admin/playlists/tri/nbformations/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR, self::PLAYLIST_NAME_BONNES_PRATIQUES);
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(3)', '2');

        // Test DESC
        $crawler = $client->request('GET', '/admin/playlists/tri/nbformations/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR, 'Développement Web');
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(3)', '3');
    }

    // --- Nouveaux Tests pour les Filtres Admin ---

    public function testAdminFilterByName(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_PLAYLISTS);

        $form = $crawler->filter('form[action*="' . self::ADMIN_PLAYLISTS . '/recherche/name"] ')->form([
            'recherche' => 'Pratiques'
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, 'table.table tbody tr');
        $this->assertSelectorTextContains(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR, self::PLAYLIST_NAME_BONNES_PRATIQUES);
    }

    public function testAdminFilterByCategory(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_PLAYLISTS);

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $categorieTest = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => 'Test']);
        $this->assertNotNull($categorieTest, 'Fixture Catégorie Test non trouvée');

        // Sélectionner le formulaire parent contenant le select
        $form = $crawler->filter('form[action*="' . self::ADMIN_PLAYLISTS . '/recherche/id/categories"] ')->form([
            'recherche' => $categorieTest->getId()
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, 'table.table tbody tr');
        $this->assertSelectorTextContains(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR, self::PLAYLIST_NAME_BONNES_PRATIQUES);
    }

    // --- Nouveau Test pour le lien Modifier ---

    public function testAdminClickEditPlaylistLink(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_PLAYLISTS);

        // Récupérer le nom de la première playlist pour vérification
        $playlistName = $crawler->filter(self::TABLE_FIRST_ROW_FIRST_CELL_SELECTOR)->text();

        // Cliquer sur le lien "Modifier" de la première playlist
        $link = $crawler->filter('table.table tbody tr:first-child td:last-child a.btn-primary')->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        // Cibler le H1 spécifique dans le conteneur principal
        $this->assertSelectorTextContains('div.container.mt-4 h1', 'Modifier la playlist');

        // 1. Sélectionner le formulaire
        try {
            $form = $crawler->selectButton('Enregistrer')->form();
        } catch (\InvalidArgumentException $e) {
            $this->fail('Bouton de soumission "Enregistrer" non trouvé dans le formulaire de modification de playlist. Vérifiez le label du bouton dans PlaylistType. Erreur: ' . $e->getMessage());
        }

        // 2. Vérifier l'URI
        $submissionUri = $form->getUri();
        $this->assertStringContainsString('/edit/', $submissionUri, "L'URI de soumission du formulaire playlist ne contient pas \"/edit/\". URI trouvée: {$submissionUri}");

        // 3. Vérifier le champ 'name'
        $this->assertInputValueSame('playlist[name]', $playlistName);
    }
}
