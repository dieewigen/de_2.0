<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class PlayerBuild implements JsonSerializable {
    private int $techId;
    private int $count;
    private int $eta;
    private int $score;

    public function __construct(int $techId, int $count, int $eta, int $score)
    {
        $this->techId = $techId;
        $this->count = $count;
        $this->eta = $eta;
        $this->score = $score;
    }
    public function jsonSerialize(): array
    {
        return [ 'techId' => $this->techId, 'count' => $this->count, 'eta' => $this->eta, 'points' => $this->score ];
    }
}