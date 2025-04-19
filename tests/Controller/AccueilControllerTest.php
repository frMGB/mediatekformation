<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Teste le contrôleur AccueilController.
 */
class AccueilControllerTest extends WebTestCase
{
    /**
     * Teste l'accessibilité et le contenu de la page d'accueil.
     */
    public function testHomepageIsAccessible(): void
    {
        $client = static::createClient();
        // Faire une requête GET sur la page d'accueil
        $crawler = $client->request('GET', '/');

        // Vérifier que la réponse est réussie (code 200)
        $this->assertResponseIsSuccessful();

        // Vérifier la présence d'un élément clé
        $this->assertSelectorTextContains('h3', 'Bienvenue sur le site de MediaTek86 consacré aux formations en ligne');
    }
}
