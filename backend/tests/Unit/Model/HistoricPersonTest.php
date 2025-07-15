<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Tests\TestCase;
use Src\Model\HistoricPerson;

class HistoricPersonTest extends TestCase
{
    public function testHistoricPersonCreation(): void
    {
        $person = new HistoricPerson(
            id: 1,
            gameStateId: 100,
            name: 'Winston Churchill',
            deathDate: '1965-01-24'
        );

        $this->assertEquals(1, $person->getId());
        $this->assertEquals(100, $person->getGameStateId());
        $this->assertEquals('Winston Churchill', $person->getName());
        $this->assertEquals('1965-01-24', $person->getDeathDate());
    }

    public function testIsAliveAtMethod(): void
    {
        $person = new HistoricPerson(
            id: 1,
            gameStateId: 100,
            name: 'Winston Churchill',
            deathDate: '1965-01-24'
        );

        // Test dates before death
        $this->assertTrue($person->isAliveAt('1950-01-01'));
        $this->assertTrue($person->isAliveAt('1964-12-31'));
        
        // Test dates after death
        $this->assertFalse($person->isAliveAt('1965-01-25'));
        $this->assertFalse($person->isAliveAt('1970-01-01'));
        
        // Test exact death date
        $this->assertFalse($person->isAliveAt('1965-01-24'));
    }

    public function testIsAliveAtWithEarlyDeath(): void
    {
        $person = new HistoricPerson(
            id: 1,
            gameStateId: 100,
            name: 'Winston Churchill',
            deathDate: '1938-01-01'
        );

        // Dies before WW2 (1939-09-01)
        $this->assertFalse($person->isAliveAt('1939-09-01'));
        $this->assertFalse($person->isAliveAt('1945-05-08'));
        
        // Alive before early death
        $this->assertTrue($person->isAliveAt('1937-01-01'));
    }

    public function testSetDeathDate(): void
    {
        $person = new HistoricPerson(
            id: 1,
            gameStateId: 100,
            name: 'Winston Churchill',
            deathDate: '1938-01-01'
        );

        $person->setDeathDate('1965-01-24');
        
        $this->assertEquals('1965-01-24', $person->getDeathDate());
        $this->assertTrue($person->isAliveAt('1945-05-08')); // VE Day
    }
}
