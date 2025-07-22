<?php

declare(strict_types=1);

namespace Src\Service;

use Src\Model\GameState;
use Src\Model\HistoricPerson;
use Src\Model\HistoricPersonTemplate;

class NewGameService
{
    public static function createNewGame(int $userId, int $campaignId = 1): GameState
    {
        $gameState = new GameState(0, $userId, $campaignId);
        
        if (!$gameState->save()) {
            throw new \Exception('Failed to create new game');
        }

        self::generateHistoricPeople($gameState);
        
        return $gameState;
    }

    private static function generateHistoricPeople(GameState $gameState): void
    {
        $templates = HistoricPersonTemplate::findByCampaignId($gameState->getCampaignId());
        
        foreach ($templates as $template) {
            $person = self::createPersonFromTemplate($template, $gameState->getId());
            if (!$person->save()) {
                throw new \Exception('Failed to create historic person: ' . $template->getName());
            }
        }
    }

    private static function createPersonFromTemplate(HistoricPersonTemplate $template, int $gameStateId): HistoricPerson
    {
        return new HistoricPerson(
            0,
            $gameStateId,
            $template->getName(),
            $template->getOriginalDeathDate(),
            $template->getAlternateDeathDate()
        );
    }

    public static function canCreateNewGame(int $userId): bool
    {
        return true;
    }

    public static function getAvailableCampaigns(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'World War II Campaign',
                'description' => 'Navigate the critical events of World War II',
                'difficulty' => 'Medium'
            ]
        ];
    }
}
