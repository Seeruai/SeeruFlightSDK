<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class OrderResponse
{
    public function __construct(
        public readonly array $data,
        public readonly ?string $error = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data,
            $data['error'] ?? null
        );
    }
} 