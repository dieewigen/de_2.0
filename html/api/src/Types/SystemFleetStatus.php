<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class SystemFleetStatus implements JsonSerializable {
    private int $sourcePlayerId;
    private int $sourcePlayerSector;
    private int $sourcePlayerSystem;
    private int $sourceAllyId;
    private int $eta;
    private int $amount;
    private int $fp;

    public function __construct(int $sourcePlayerId, int $sourcePlayerSector, int $sourcePlayerSystem, int $sourceAllyId, int $eta,
                                int $amount, int $fp)
    {
        $this->sourcePlayerId = $sourcePlayerId;
        $this->sourcePlayerSector = $sourcePlayerSector;
        $this->sourcePlayerSystem = $sourcePlayerSystem;
        $this->eta = $eta;
        $this->amount = $amount;
        $this->fp = $fp;
        $this->sourceAllyId = $sourceAllyId;
    }

    public function jsonSerialize(): array
    {
        return [ 'sPlayerId' => $this->sourcePlayerId, 'sPlayerSec' => $this->sourcePlayerSector,
            'sPlayerSys' => $this->sourcePlayerSystem,'sAllyId' => $this->sourceAllyId ,
            'eta' => $this->eta, 'amount' => $this->amount, 'fp' => $this->fp];
    }
}