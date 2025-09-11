<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class SectorSystemStatus implements JsonSerializable {
    private int $targetSector;
    private int $targetSystem;
    private int $targetPlayerId;
    private int $targetAllyId;
    private array $attFleets;
    private array $defFleets;

    public function __construct(int $targetSector, int $targetSystem, int $targetPlayerId, int $targetAllyId,
                                array $attFleets, array $defFleets)
    {
        $this->targetSector = $targetSector;
        $this->targetSystem = $targetSystem;
        $this->targetPlayerId = $targetPlayerId;
        $this->attFleets = $attFleets;
        $this->defFleets = $defFleets;
        $this->targetAllyId = $targetAllyId;
    }

    public function jsonSerialize(): array
    {
        return ['tSec' => $this->targetSector, 'tSys' => $this->targetSystem, 'tPlayerId' => $this->targetPlayerId,
            'tAllyId' => $this->targetAllyId, 'attFleets' => $this->attFleets, 'defFleets' => $this->defFleets];
    }
}