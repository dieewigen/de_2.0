<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class AllianceWar implements JsonSerializable
{
    private int $attackerId;
    private int $defenderId;
    private bool $peaceOffer;

    public function __construct(int $attackerId, int $defenderId, bool $peaceOffer)
    {
        $this->attackerId = $attackerId;
        $this->defenderId = $defenderId;
        $this->peaceOffer = $peaceOffer;
    }

    public function jsonSerialize(): array
    {
        return [
            'attackerId' => $this->attackerId,
            'defenderId' => $this->defenderId,
            'peaceOffer' => $this->peaceOffer,
        ];
    }
}
