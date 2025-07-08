<?php

class TechDataResponse implements JsonSerializable
{
    private array $techs;

    public function __construct(array $techs)
    {
        $this->techs = $techs;
    }

    public function getTechs(): array
    {
        return $this->techs;
    }

    public function jsonSerialize()
    {
        return ['techs' => $this->techs];
    }

}
