<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Entity;

final class PublisherConfig
{
    public function __construct(
        private string $key,
        private string $value,
        public string $purposeType = 'default',
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPurposeType(): string
    {
        return $this->purposeType;
    }

    public function asArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'purpose_type' => $this->purposeType,
        ];
    }
}
