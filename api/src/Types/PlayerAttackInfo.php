<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class PlayerAttackInfo implements JsonSerializable {
    private int $id;
    private int $sector;
    private int $system;
    private string $name;
    private int $points;
    private int $fleetPoints;
    private int $collectors;
    private int $race;
    private bool $canBeAttacked;

    public function __construct(int $id, int $sector, int $system, string $name, int $points, int $fleetPoints,
                                int $collectors, int $race, bool $canBeAttacked)
    {
        $this->id = $id;
        $this->sector = $sector;
        $this->system = $system;
        $this->name = $name;
        $this->points = $points;
        $this->fleetPoints = $fleetPoints;
        $this->collectors = $collectors;
        $this->race = $race;
        $this->canBeAttacked = $canBeAttacked;
    }

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'sector' => $this->sector, 'system' => $this->system, 'name' => $this->name,
            'points' => $this->points, 'fpoints' => $this->fleetPoints, 'cols' => $this->collectors,
            'canBeAttacked' => $this->canBeAttacked, 'race' => $this->race];
    }
}