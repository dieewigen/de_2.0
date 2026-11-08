<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class SectorStatus implements JsonSerializable {
    private array $sectorSystemStatus;
    private array $sectorFleets;

    public function __construct(array $sectorSystemStatus, array $sectorFleets)
    {
        $this->sectorSystemStatus = $sectorSystemStatus;
        $this->sectorFleets = $sectorFleets;
    }

    public function jsonSerialize(): array
    {
        return [ 'sectorSystemStatus' => $this->sectorSystemStatus, 'sectorFleets' => $this->sectorFleets];
    }
}