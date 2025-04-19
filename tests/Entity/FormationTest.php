<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Teste l'entité Formation.
 */
class FormationTest extends TestCase
{
    /**
     * Teste si getPublishedAtString retourne la date au format correct lorsqu'une date valide est définie.
     */
    public function testGetPublishedAtStringWithValidDate(): void
    {
        $formation = new Formation();
        $date = new DateTime('2025-01-04');
        $formation->setPublishedAt($date);

        $this->assertEquals('04/01/2025', $formation->getPublishedAtString());
    }

    /**
     * Teste si getPublishedAtString retourne une chaîne vide lorsque la date est nulle.
     */
    public function testGetPublishedAtStringWithNullDate(): void
    {
        $formation = new Formation();
        $formation->setPublishedAt(null);

        $this->assertEquals('', $formation->getPublishedAtString());
    }
}
