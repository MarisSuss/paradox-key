<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Tests\TestCase;
use Src\Model\HistoricEvent;

class HistoricEventTest extends TestCase
{
    public function testHistoricEventCreation(): void
    {
        $event = new HistoricEvent(1, 'World War II', '1939-09-01');
        
        $this->assertEquals(1, $event->getId());
        $this->assertEquals('World War II', $event->getName());
        $this->assertEquals('1939-09-01', $event->getDate());
        $this->assertNotEmpty($event->getCreatedAt());
    }

    public function testHistoricEventWithEmptyValues(): void
    {
        $event = new HistoricEvent(0, '', '');
        
        $this->assertEquals(0, $event->getId());
        $this->assertEquals('', $event->getName());
        $this->assertEquals('', $event->getDate());
        $this->assertNotEmpty($event->getCreatedAt());
    }

    public function testHistoricEventWithLongValues(): void
    {
        $longName = str_repeat('a', 200);
        $createdAt = '2025-01-01 12:00:00';
        
        $event = new HistoricEvent(999, $longName, '1945-05-08', $createdAt);
        
        $this->assertEquals(999, $event->getId());
        $this->assertEquals($longName, $event->getName());
        $this->assertEquals('1945-05-08', $event->getDate());
        $this->assertEquals($createdAt, $event->getCreatedAt());
    }

    public function testHistoricEventDateFormat(): void
    {
        $event = new HistoricEvent(1, 'Test Event', '2025-12-31');
        
        $this->assertEquals('2025-12-31', $event->getDate());
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $event->getDate());
    }

    public function testHistoricEventEquality(): void
    {
        $event1 = new HistoricEvent(1, 'World War II', '1939-09-01');
        $event2 = new HistoricEvent(1, 'World War II', '1939-09-01');
        $event3 = new HistoricEvent(2, 'Different Event', '1940-01-01');
        
        $this->assertEquals($event1->getId(), $event2->getId());
        $this->assertEquals($event1->getName(), $event2->getName());
        $this->assertEquals($event1->getDate(), $event2->getDate());
        $this->assertNotEquals($event1->getId(), $event3->getId());
    }

    public function testHistoricEventCreatedAtDefault(): void
    {
        $event = new HistoricEvent(1, 'Test Event', '2025-01-01');
        
        $this->assertNotEmpty($event->getCreatedAt());
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $event->getCreatedAt());
    }
}
