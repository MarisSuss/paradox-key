<?php

declare(strict_types=1);

namespace Tests\Unit\Collection;

use Tests\TestCase;
use Src\Model\DialoguePrompt;
use Src\Model\DialogueResponse;
use Src\Collection\DialoguePromptCollection;
use Src\Collection\DialogueResponseCollection;

class DialogueCollectionTest extends TestCase
{
    public function testDialoguePromptCollection(): void
    {
        $prompt1 = new DialoguePrompt(1, 1, 'Save my world!', 'Context 1', 'easy');
        $prompt2 = new DialoguePrompt(2, 1, 'Help me understand', 'Context 2', 'medium');
        $prompt3 = new DialoguePrompt(3, 2, 'Different campaign', 'Context 3', 'hard');
        
        $collection = new DialoguePromptCollection([$prompt1, $prompt2, $prompt3]);
        
        $this->assertEquals(3, $collection->count());
        $this->assertFalse($collection->isEmpty());
        $this->assertEquals($prompt1, $collection->getById(1));
        
        // Test filtering by difficulty
        $easyPrompts = $collection->filterByDifficulty('easy');
        $this->assertEquals(1, $easyPrompts->count());
        
        // Test filtering by campaign
        $campaign1Prompts = $collection->filterByCampaign(1);
        $this->assertEquals(2, $campaign1Prompts->count());
        
        // Test random selection
        $random = $collection->getRandom();
        $this->assertInstanceOf(DialoguePrompt::class, $random);
    }

    public function testDialogueResponseCollection(): void
    {
        $response1 = new DialogueResponse(1, 1, 'Helpful response', 'helpful', 5.0);
        $response2 = new DialogueResponse(2, 1, 'Neutral response', 'neutral', 0.0);
        $response3 = new DialogueResponse(3, 1, 'Harmful response', 'harmful', -10.0);
        
        $collection = new DialogueResponseCollection([$response1, $response2, $response3]);
        
        $this->assertEquals(3, $collection->count());
        $this->assertFalse($collection->isEmpty());
        
        // Test filtering by outcome
        $helpful = $collection->getHelpful();
        $this->assertEquals(1, $helpful->count());
        
        $harmful = $collection->getHarmful();
        $this->assertEquals(1, $harmful->count());
        
        $neutral = $collection->getNeutral();
        $this->assertEquals(1, $neutral->count());
        
        // Test timeline impact calculations
        $this->assertEquals(-5.0, $collection->getTotalTimelineImpact());
        $this->assertEquals(-5.0/3, $collection->getAverageTimelineImpact());
        
        // Test positive/negative impact filtering
        $positive = $collection->filterByPositiveImpact();
        $this->assertEquals(1, $positive->count());
        
        $negative = $collection->filterByNegativeImpact();
        $this->assertEquals(1, $negative->count());
    }

    public function testEmptyCollections(): void
    {
        $promptCollection = new DialoguePromptCollection();
        $this->assertTrue($promptCollection->isEmpty());
        $this->assertEquals(0, $promptCollection->count());
        $this->assertNull($promptCollection->getRandom());
        
        $responseCollection = new DialogueResponseCollection();
        $this->assertTrue($responseCollection->isEmpty());
        $this->assertEquals(0, $responseCollection->count());
        $this->assertEquals(0.0, $responseCollection->getTotalTimelineImpact());
        $this->assertEquals(0.0, $responseCollection->getAverageTimelineImpact());
    }
}
