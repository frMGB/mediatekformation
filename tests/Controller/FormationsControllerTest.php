<?php
// tests/Controller/FormationsControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class FormationsControllerTest extends WebTestCase
{
    private const FIRST_ROW_FIRST_CELL_SELECTOR = 'table.table tbody tr:first-child td:first-child h5';
    private const FIRST_ROW_SECOND_CELL_SELECTOR = 'table.table tbody tr:first-child td:nth-child(2)';
    private const FORMATIONS = '/formations';
    private const TABLE_ROW_SELECTOR = 'table.table tbody tr';

    // Test tri par titre
    public function testSortByTitle(): void
    {
        $client = static::createClient();

        // Test ASC
        $crawler = $client->request('GET', '/formations/tri/title/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'Bases HTML5 et CSS3');

        // Test DESC
        $crawler = $client->request('GET', '/formations/tri/title/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'Tests Unitaires en PHP avec PHPUnit');
    }

    // Test tri par nom de playlist
    public function testSortByPlaylistName(): void
    {
        $client = static::createClient();

        // Test ASC
        $crawler = $client->request('GET', '/formations/tri/name/ASC/playlist');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_SECOND_CELL_SELECTOR, 'Bonnes Pratiques');

        // Test DESC
        $crawler = $client->request('GET', '/formations/tri/name/DESC/playlist');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::FIRST_ROW_SECOND_CELL_SELECTOR, 'Développement Web');
    }

    // Test tri par date de publication
    public function testSortByPublishedAt(): void
    {
        $client = static::createClient();

        // Test ASC
        $crawler = $client->request('GET', '/formations/tri/publishedAt/ASC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(4)', '15/01/2024');

        // Test DESC
        $crawler = $client->request('GET', '/formations/tri/publishedAt/DESC');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(4)', '11/04/2024');
    }

    // --- Nouveaux Tests pour les Filtres ---

    public function testFilterByTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::FORMATIONS);

        $form = $crawler->filter('form[action*="/formations/recherche/title"] ')->form([
            'recherche' => 'Symfony'
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(2, self::TABLE_ROW_SELECTOR);
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'Symfony');
    }

    public function testFilterByPlaylistName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::FORMATIONS);

        $form = $crawler->filter('form[action*="/formations/recherche/name/playlist"] ')->form([
            'recherche' => 'Pratiques'
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(2, self::TABLE_ROW_SELECTOR);
        $this->assertSelectorTextContains(self::FIRST_ROW_SECOND_CELL_SELECTOR, 'Bonnes Pratiques');
    }

    public function testFilterByCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::FORMATIONS);

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $categorieTest = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => 'Test']);
        $this->assertNotNull($categorieTest, 'Fixture Catégorie Test non trouvée');

        $form = $crawler->filter('form[action*="/formations/recherche/id/categories"] ')->form([
            'recherche' => $categorieTest->getId()
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, self::TABLE_ROW_SELECTOR);
        $this->assertSelectorTextContains(self::FIRST_ROW_FIRST_CELL_SELECTOR, 'Tests Unitaires en PHP avec PHPUnit');
        $this->assertSelectorTextContains('table.table tbody tr:first-child td:nth-child(3)', 'Test');
    }

    // --- Nouveau Test pour le lien de détail ---

    public function testClickFormationLink(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::FORMATIONS);

        $link = $crawler->filter('table.table tbody tr:first-child td:nth-child(5) a')->link();
        $formationTitle = $crawler->filter(self::FIRST_ROW_FIRST_CELL_SELECTOR)->text();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', $formationTitle);
    }
}
