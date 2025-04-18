<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use DateTime;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtStringWithValidDate(): void
    {
        $formation = new Formation();
        $date = new DateTime('2025-01-04');
        $formation->setPublishedAt($date);

        $this->assertEquals('04/01/2025', $formation->getPublishedAtString());
    }

    public function testGetPublishedAtStringWithNullDate(): void
    {
        $formation = new Formation();
        $formation->setPublishedAt(null);

        $this->assertEquals('', $formation->getPublishedAtString());
    }
}
