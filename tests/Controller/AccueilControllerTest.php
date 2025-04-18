<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccueilControllerTest extends WebTestCase
{
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
