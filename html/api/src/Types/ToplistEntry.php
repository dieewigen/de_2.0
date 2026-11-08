<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class ToplistEntry implements JsonSerializable {
    private int $id;
    private int $points;
    private int $fleetPoints;
    private int $ehPoints;
    private int $collectors;
    private int $sector;
    private int $system;
    private int $allyId;

    public function __construct(int $id, int $points, int $fleetPoints, int $collectors, int $ehPoints, int $sector,
                                int $system, int $allyId)
    {
        $this->id = $id;
        $this->points = $points;
        $this->fleetPoints = $fleetPoints;
        $this->collectors = $collectors;
        $this->ehPoints = $ehPoints;
        $this->sector = $sector;
        $this->system = $system;
        $this->allyId = $allyId;
    }

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'points' => $this->points, 'fpoints' => $this->fleetPoints,
            'cols' => $this->collectors, 'ehpoints' => $this->ehPoints, 'sector' => $this->sector,
            'system' => $this->system, 'allyId' => $this->allyId];
    }
}