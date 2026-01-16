<?php

namespace DieEwigen\DE2\Model\Npc\Types;

class FleetPreset
{
    private string $id;
    private string $name;
    private array $unitRatios;
    private array $unitRatiosByName;

    public function __construct(string $id, string $name, array $unitRatios, array $unitRatiosByName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->unitRatios = $unitRatios;
        $this->unitRatiosByName = $unitRatiosByName;
    }

    /**
     * Returns a map of ship ID and ratio in relation to the ship type with highest number
     * @return array<string, double>
     */
    public function getShipRatios(): array
    {
        return $this->unitRatios;
    }

    /**
     * Returns a map of ship name and ratio in relation to the ship type with highest number
     * @return array
     */
    public function getShipRatiosByName(): array
    {
        return $this->unitRatiosByName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
