<?php

namespace App\Model;

class Dialogue
{
    private $id;
    private $messages;

    public function __construct($id, $messages = [])
    {
        $this->id = $id;
        $this->messages = $messages;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getMessages()
    {
        return $this->messages;
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }
}