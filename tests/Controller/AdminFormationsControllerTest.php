<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use App\Entity\Playlist;
use App\Entity\Formation;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\Categorie;

class AdminFormationsControllerTest extends WebTestCase
{
    private const ADMIN_FORMATIONS = '/admin/formations';
    private const FIRST_ROW_FIRST_CELL_SELECTOR = 'table.table tbody tr:first-child td:first-child';
    private const FIRST_ROW_SECOND_CELL_SELECTOR = 'table.table tbody tr:first-child td:nth-child(2)';
    private const TABLE_ROW_SELECTOR = 'table.table tbody tr';

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

    public function testAjoutFormationDateFuture(): void
    {
        $client = $this->createAuthenticatedClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $playlistRepository = $entityManager->getRepository(Playlist::class);

        // Récupérer une playlist existante
        $playlist = $playlistRepository->findOneBy([]);
        if (!$playlist) {
            $this->markTestSkipped('Aucune playlist trouvée dans la base de données de test.');
        }

        $urlGenerator = $container->get('router');
        $addUrl = $urlGenerator->generate('admin.formations.ajout');

        $crawler = $client->request('GET', $addUrl);

        // Vérifier que la page se charge correctement
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Ajouter une Formation');

        // Préparer les données du formulaire
        $futureDate = (new DateTime('tomorrow'))->format('Y-m-d');
        $formData = [
            'formation[title]' => 'Formation Test Date Future',
            'formation[description]' => 'Test de validation de date',
            'formation[videoId]' => 'abcdef123',
            'formation[playlist]' => $playlist->getId(),
            'formation[publishedAt]' => $futureDate,
        ];

        $form = $crawler->selectButton('Enregistrer')->form($formData);
        $client->submit($form);

        $this->assertFalse($client->getResponse()->isRedirect(), "Ne devrait pas rediriger après une soumission invalide.");

        $this->assertStringContainsString('La date ne peut pas être postérieure à aujourd&#039;hui.', $client->getResponse()->getContent());

        $formationRepository = $entityManager->getRepository(Formation::class);
        $formation = $formationRepository->findOneBy(['title' => 'Formation Test Date Future']);
        $this->assertNull($formation, 'Une formation avec une date future ne devrait pas être enregistrée.');
    }

    // --- Nouveaux Tests pour les Tris ---

    public function testAdminSortByTitle(): void
    {
        $client = $this->createAuthenticatedClient();

        // Test ASC
        $crawler = $client->request('GET', '/admin/formations/tri/title/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'Bases HTML5 et CSS3');

        // Test DESC
        $crawler = $client->request('GET', '/admin/formations/tri/title/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'Tests Unitaires en PHP avec PHPUnit');
    }

    public function testAdminSortByPlaylistName(): void
    {
        $client = $this->createAuthenticatedClient();

        // Test ASC
        $crawler = $client->request('GET', '/admin/formations/tri/name/ASC/playlist');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_SECOND_CELL_SELECTOR, 'Bonnes Pratiques');

        // Test DESC
        $crawler = $client->request('GET', '/admin/formations/tri/name/DESC/playlist');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_SECOND_CELL_SELECTOR, 'Développement Web');
    }

    public function testAdminSortByPublishedAt(): void
    {
        $client = $this->createAuthenticatedClient();

        // Test ASC
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(4)', '15/01/2024');

        // Test DESC
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(4)', '11/04/2024');
    }

    // --- Nouveaux Tests pour les Filtres Admin ---

    public function testAdminFilterByTitle(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_FORMATIONS);

        $form = $crawler->filter('form[action*="/admin/formations/recherche/title"] ')->form([
            'recherche' => 'PHP'
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(2, self::TABLE_ROW_SELECTOR);
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'PHP');
    }

    public function testAdminFilterByPlaylistName(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_FORMATIONS);

        $form = $crawler->filter('form[action*="/admin/formations/recherche/name/playlist"] ')->form([
            'recherche' => 'Web'
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(3, self::TABLE_ROW_SELECTOR);
        $this->assertSelectorTextContains(self::FIRST_ROW_SECOND_CELL_SELECTOR, 'Développement Web');
    }

    public function testAdminFilterByCategory(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_FORMATIONS);

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $categorieSymfony = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => 'Symfony']);
        $this->assertNotNull($categorieSymfony, 'Fixture Catégorie Symfony non trouvée');

        // Sélectionner le formulaire parent contenant le select
        $form = $crawler->filter('form[action*="/admin/formations/recherche/id/categories"]')->form([
            'recherche' => $categorieSymfony->getId()
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(2, self::TABLE_ROW_SELECTOR);
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(3)', 'Symfony');
    }

    // --- Nouveau Test pour le lien Modifier ---

    public function testAdminClickEditFormationLink(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ADMIN_FORMATIONS);

        $formationTitle = $crawler->filter(self::FIRST_ROW_FIRST_CELL_SELECTOR)->text();

        // Cliquer sur le lien "Modifier" de la première formation
        $link = $crawler->filter('table.table tbody tr:first-child td:last-child a.btn-primary')->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        // Cibler le H1 spécifique dans le conteneur principal
        $this->assertSelectorTextContains('div.container.mt-4 h1', 'Modifier la formation');

        // 1. Sélectionner le formulaire
        try {
            $form = $crawler->selectButton('Enregistrer')->form();
        } catch (\InvalidArgumentException $e) {
            $this->fail('Bouton de soumission "Enregistrer" non trouvé dans le formulaire de modification de formation. Vérifiez le label du bouton dans FormationType. Erreur: ' . $e->getMessage());
        }

        // 2. Vérifier l'URI
        $submissionUri = $form->getUri();
        $this->assertStringContainsString('/edit/', $submissionUri, "L'URI de soumission du formulaire formation ne contient pas \"/edit/\". URI trouvée: {$submissionUri}");

        // 3. Vérifier le champ 'title'
        $this->assertInputValueSame('formation[title]', $formationTitle);
    }
}
