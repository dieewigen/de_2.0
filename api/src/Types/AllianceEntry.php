<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class AllianceEntry implements JsonSerializable
{
    private int $id;
    private string $tag;
    private string $name;
    private bool $npcAlly;
    private int $openSlots;
    private int $leaderId;

    public function __construct(int $id, string $tag, string $name, bool $npcAlly, int $openSlots, int $leaderId)
    {
        $this->id = $id;
        $this->tag = $tag;
        $this->name = $name;
        $this->npcAlly = $npcAlly;
        $this->openSlots = $openSlots;
        $this->leaderId = $leaderId;
    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'tag' => $this->tag, 'name' => $this->name,
            'npcAlly' => $this->npcAlly, 'openSlots' => $this->openSlots, 'leaderId' => $this->leaderId];
    }
}