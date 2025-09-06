<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class Resources implements JsonSerializable {
    private int $m;
    private int $d;
    private int $i;
    private int $e;
    private int $t;

    public function __construct(int $m, int $d, int $i, int $e, int $t)
    {
        $this->m = $m;
        $this->d = $d;
        $this->i = $i;
        $this->e = $e;
        $this->t = $t;
    }
    public function jsonSerialize(): array
    {
        return [ 'm' => $this->m, 'd' => $this->d, 'i' => $this->i, 'e' => $this->e,
            't' => $this->t];
    }
}