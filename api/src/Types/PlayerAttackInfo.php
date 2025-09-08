<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class PlayerAttackInfo implements JsonSerializable {
    private int $id;
    private int $sector;
    private int $system;
    private int $points;
    private int $fleetPoints;
    private int $collectors;
    private int $race;
    private bool $canBeAttacked;

    public function __construct(int $id, int $sector, int $system, int $points, int $fleetPoints,
                                int $collectors, int $race, bool $canBeAttacked)
    {
        $this->id = $id;
        $this->sector = $sector;
        $this->system = $system;
        $this->points = $points;
        $this->fleetPoints = $fleetPoints;
        $this->collectors = $collectors;
        $this->race = $race;
        $this->canBeAttacked = $canBeAttacked;
    }

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'sector' => $this->sector, 'system' => $this->system,
            'points' => $this->points, 'fpoints' => $this->fleetPoints, 'cols' => $this->collectors,
            'canBeAttacked' => $this->canBeAttacked, 'race' => $this->race];
    }
}
