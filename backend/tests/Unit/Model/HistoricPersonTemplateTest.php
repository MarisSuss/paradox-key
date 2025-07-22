<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Tests\TestCase;
use Src\Model\HistoricPersonTemplate;

class HistoricPersonTemplateTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $alternateDeathDates = ['1965-01-24', '1955-04-15', '1970-12-31'];
        
        $template = new HistoricPersonTemplate(
            1,
            1,
            'Winston Churchill',
            '1938-01-01',
            $alternateDeathDates
        );
        
        $this->assertEquals(1, $template->getId());
        $this->assertEquals(1, $template->getCampaignId());
        $this->assertEquals('Winston Churchill', $template->getName());
        $this->assertEquals('1938-01-01', $template->getOriginalDeathDate());
        $this->assertEquals($alternateDeathDates, $template->getAlternateDeathDates());
    }

    public function testGetRandomAlternateDeathDate(): void
    {
        $alternateDeathDates = ['1965-01-24', '1955-04-15', '1970-12-31'];
        
        $template = new HistoricPersonTemplate(
            1,
            1,
            'Winston Churchill',
            '1938-01-01',
            $alternateDeathDates
        );
        
        $randomDate = $template->getRandomAlternateDeathDate();
        $this->assertContains($randomDate, $alternateDeathDates);
    }

    public function testGetRandomAlternateDeathDateWithEmptyArray(): void
    {
        $template = new HistoricPersonTemplate(
            1,
            1,
            'Test Person',
            '1940-01-01',
            []
        );
        
        $result = $template->getRandomAlternateDeathDate();
        $this->assertEquals('1940-01-01', $result); // Should return original death date
    }

    public function testGetAlternateDeathDateCallsRandom(): void
    {
        $alternateDeathDates = ['1965-01-24'];
        
        $template = new HistoricPersonTemplate(
            1,
            1,
            'Winston Churchill',
            '1938-01-01',
            $alternateDeathDates
        );
        
        $result = $template->getAlternateDeathDate();
        $this->assertEquals('1965-01-24', $result);
    }
}
