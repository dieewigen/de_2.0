<?php

namespace DieEwigen\DE2\Model\Npc\Types;

class FleetPresetConfig
{
    private ?string $currentPresetId;
    private array $presets;

    public function __construct(?string $currentPresetId, array $presets)
    {
        $this->currentPresetId = $currentPresetId;
        $this->presets = $presets;
    }

    public function getPresets(): array
    {
        return $this->presets;
    }

    public function getCurrentPresetId(): ?string
    {
        return $this->currentPresetId;
    }

}
