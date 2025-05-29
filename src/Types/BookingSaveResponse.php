<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class BookingSaveResponse
{
    public function __construct(
        public readonly string $orderId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['order_id']
        );
    }
} 