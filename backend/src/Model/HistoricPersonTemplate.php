<?php

declare(strict_types=1);

namespace Src\Model;

class HistoricPersonTemplate
{
    private int $id;
    private string $name;
    private string $deathDate;

    public function __construct(int $id, string $name, string $deathDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->deathDate = $deathDate;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDeathDate(): string
    {
        return $this->deathDate;
    }
}
