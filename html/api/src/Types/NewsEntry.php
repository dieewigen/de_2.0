<?php

namespace DieEwigen\Api\Types;

use JsonSerializable;

class NewsEntry implements JsonSerializable {
    private int $typ;
    private string $time;
    private string $text;

    public function __construct(int $typ, string $time, string $text)
    {
        $this->typ = $typ;
        $this->time = $time;
        $this->text = $text;
    }

    public function jsonSerialize(): array
    {
        return [ 'type' => $this->typ, 'time' => $this->time, 'text' => $this->text];
    }
}