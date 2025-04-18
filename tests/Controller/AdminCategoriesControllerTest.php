<?php
// tests/Controller/AdminCategoriesControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;

class AdminCategoriesControllerTest extends WebTestCase
{
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

    // Test pour les liens de suppression des catégories
    public function testAdminCategoryDeleteLinks(): void
    {
        $client = $this->createAuthenticatedClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // S'assurer qu'une catégorie vide existe pour le test
        $emptyCategoryName = 'Categorie Vide Pour Test Suppr';
        $categoryWithoutFormations = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => $emptyCategoryName]);
        if (!$categoryWithoutFormations) {
            $categoryWithoutFormations = new Categorie();
            $categoryWithoutFormations->setName($emptyCategoryName);
            $entityManager->persist($categoryWithoutFormations);
            $entityManager->flush();
        }

        // Recharger le crawler APRES ajout potentiel de la catégorie
        $crawler = $client->request('GET', '/admin/categories');

        // 1. Vérifier la catégorie SANS formations
        $this->assertNotNull($categoryWithoutFormations, 'Impossible de trouver ou créer une catégorie vide pour le test.');

        $rowXPathWithout = sprintf('//table/tbody/tr[td[normalize-space(.)="%s"]]', $emptyCategoryName);
        $categoryRowWithout = $crawler->filterXPath($rowXPathWithout);
        $this->assertGreaterThan(0, $categoryRowWithout->count(), "Ligne du tableau non trouvée pour la catégorie vide: {$emptyCategoryName}");

        $this->assertNotEmpty($categoryRowWithout->filter('a.btn-danger'), 'Le lien Supprimer (a.btn-danger) devrait exister pour la catégorie vide.');
        $this->assertEmpty($categoryRowWithout->filter('button.btn-danger[disabled]'), 'Le bouton Supprimer désactivé ne devrait PAS exister pour la catégorie vide.');
        $this->assertStringContainsString('/admin/categories/suppr/' . $categoryWithoutFormations->getId(), $categoryRowWithout->filter('a.btn-danger')->attr('href'));


        // 2. Vérifier une catégorie AVEC formations
        $categoryWithFormationsName = 'PHP';
        $categoryWithFormations = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => $categoryWithFormationsName]);
        $this->assertNotNull($categoryWithFormations, 'Fixture Catégorie PHP non trouvée pour le test.');

        $rowXPathWith = sprintf('//table/tbody/tr[td[normalize-space(.)="%s"]]', $categoryWithFormationsName); // XPath
        $categoryRowWith = $crawler->filterXPath($rowXPathWith);
        $this->assertGreaterThan(0, $categoryRowWith->count(), "Ligne du tableau non trouvée pour la catégorie avec formations: {$categoryWithFormationsName}");


        $this->assertEmpty($categoryRowWith->filter('a.btn-danger'), 'Le lien Supprimer (a.btn-danger) ne devrait PAS exister pour une catégorie avec formations.');
        $this->assertNotEmpty($categoryRowWith->filter('button.btn-danger[disabled]'), 'Le bouton Supprimer désactivé devrait exister pour une catégorie avec formations.');
    }
}
