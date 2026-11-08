<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class Technology implements JsonSerializable {
    private int $id;
    private Resources $resources;

    public function __construct(int $id, Resources $resources)
    {
        $this->id = $id;
        $this->resources = $resources;
    }


    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'res' => $this->resources];
    }
}