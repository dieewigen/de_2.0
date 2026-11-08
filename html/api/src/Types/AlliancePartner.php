<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class AlliancePartner implements JsonSerializable
{
    private int $allyId1;
    private int $allyId2;

    public function __construct(int $allyId1, int $allyId2)
    {
        $this->allyId1 = $allyId1;
        $this->allyId2 = $allyId2;
    }

    public function jsonSerialize(): array
    {
        return ['allyId1' => $this->allyId1, 'allyId2' => $this->allyId2];
    }
}
