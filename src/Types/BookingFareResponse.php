<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class BookingFareResponse
{
    public function __construct(
        public readonly string $status,
        public readonly array $booking,
        public readonly ?string $error = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['status'],
            $data['booking'] ?? [],
            $data['error'] ?? null
        );
    }
} 