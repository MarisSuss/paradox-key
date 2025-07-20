<?php

namespace Src\Model;

class PlayerAnswers
{
    private int $id;
    private string $answer;

    public function __construct(
        int $id,
        string $answer
    ) {
        $this->id = $id;
        $this->answer = $answer;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }
}
