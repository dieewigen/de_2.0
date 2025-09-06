<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class SectorSystemStatus implements JsonSerializable {
    private int $targetSector;
    private int $targetSystem;
    private int $targetPlayerId;
    private array $attFleets;
    private array $defFleets;

    public function __construct(int $targetSector, int $targetSystem, int $targetPlayerId, array $attFleets, array $defFleets)
    {
        $this->targetSector = $targetSector;
        $this->targetSystem = $targetSystem;
        $this->targetPlayerId = $targetPlayerId;
        $this->attFleets = $attFleets;
        $this->defFleets = $defFleets;
    }

    public function jsonSerialize(): array
    {
        return ['tSec' => $this->targetSector, 'tSys' => $this->targetSystem, 'tPlayerId' => $this->targetPlayerId,
            'attFleets' => $this->attFleets, 'defFleets' => $this->defFleets];
    }
}