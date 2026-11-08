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
    private int $artefacts;
    private int $tronics;
    private array $buildings;

    public function __construct(int $id, string $tag, string $name, bool $npcAlly, int $openSlots, int $leaderId,
                                int $artefacts = 0, int $tronics = 0, $buildings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0])
    {
        $this->id = $id;
        $this->tag = $tag;
        $this->name = $name;
        $this->npcAlly = $npcAlly;
        $this->openSlots = $openSlots;
        $this->leaderId = $leaderId;
        $this->artefacts = $artefacts;
        $this->tronics = $tronics;
        $this->buildings = $buildings;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'tag' => $this->tag, 'name' => $this->name,
            'npcAlly' => $this->npcAlly, 'openSlots' => $this->openSlots, 'leaderId' => $this->leaderId,
            'artefacts' => $this->artefacts, 'tronics' => $this->tronics,
            'buildings' => $this->buildings];
    }
}