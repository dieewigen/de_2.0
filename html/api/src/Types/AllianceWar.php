<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class AllianceWar implements JsonSerializable
{
    private int $attackerId;
    private int $defenderId;
    private bool $peaceOffer;
    private int $peaceOfferBy;

    public function __construct(int $attackerId, int $defenderId, bool $peaceOffer, int $peaceOfferBy)
    {
        $this->attackerId = $attackerId;
        $this->defenderId = $defenderId;
        $this->peaceOffer = $peaceOffer;
        $this->peaceOfferBy = $peaceOfferBy;
    }

    public function jsonSerialize(): array
    {
        return [
            'attackerId' => $this->attackerId,
            'defenderId' => $this->defenderId,
            'peaceOffer' => $this->peaceOffer,
            'peaceOfferBy' => $this->peaceOfferBy
        ];
    }
}
