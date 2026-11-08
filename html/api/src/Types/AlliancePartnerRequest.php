<?php

namespace DieEwigen\Api\Types;

class AlliancePartnerRequest implements \JsonSerializable
{
    public function __construct(
        private int $requestingAllyId,
        private int $receivingAllyId,
        private string $applicationText,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'requestingAllyId' => $this->requestingAllyId,
            'receivingAllyId'  => $this->receivingAllyId,
            'applicationText'  => $this->applicationText,
        ];
    }
}
