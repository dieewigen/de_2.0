<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class Resources implements JsonSerializable {
    private int $m;
    private int $d;
    private int $i;
    private int $e;
    private int $t;
    private int $cores;

    public function __construct(int $m, int $d, int $i, int $e, int $t, int $cores = 0)
    {
        $this->m = $m;
        $this->d = $d;
        $this->i = $i;
        $this->e = $e;
        $this->t = $t;
        $this->cores = $cores;
    }
    public function jsonSerialize(): array
    {
        return [ 'm' => $this->m, 'd' => $this->d, 'i' => $this->i, 'e' => $this->e,
            't' => $this->t, 'cores' => $this->cores];
    }
}