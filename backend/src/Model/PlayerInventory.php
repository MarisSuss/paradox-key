<?php

namespace Src\Model;

class PlayerInventory
{
    private int $id;
    private int $playerId;
    private int $probeCount = 0;

    public function __construct(int $id, int $playerId, int $probeCount = 0)
    {
        $this->id = $id;
        $this->playerId = $playerId;
        $this->probeCount = $probeCount;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    public function getProbeCount(): int
    {
        return $this->probeCount;
    }
}

