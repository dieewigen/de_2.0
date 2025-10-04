<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class SectorFleet implements JsonSerializable {
    private int $sourcePlayerSector;
    private int $sourcePlayerSystem;
    private int $eta;
    private int $fp;
    private int $action;

    public function __construct(int $sourcePlayerSector, int $sourcePlayerSystem, int $eta, int $fp, int $action)
    {
        $this->sourcePlayerSector = $sourcePlayerSector;
        $this->sourcePlayerSystem = $sourcePlayerSystem;
        $this->eta = $eta;
        $this->fp = $fp;
        $this->action = $action;
    }

    public function jsonSerialize(): array
    {
        return ['sPlayerSec' => $this->sourcePlayerSector, 'sPlayerSys' => $this->sourcePlayerSystem, 'eta' => $this->eta, 'fp' => $this->fp,
            'action' => $this->action];
    }
}