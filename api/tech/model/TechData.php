<?php

class TechData implements JsonSerializable
{
    private int $techId;
    private array $deps;

    public function __construct(int $techId, array $deps)
    {
        $this->techId = $techId;
        $this->deps = $deps;
    }

    public function techId(): int
    {
        return $this->techId;
    }

    public function deps(): array
    {
        return $this->deps;
    }

    public function jsonSerialize()
    {
        return ['techId' => $this->techId, 'deps' => $this->deps];
    }
}
