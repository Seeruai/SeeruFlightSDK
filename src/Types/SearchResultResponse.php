<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class SearchResultResponse
{
    public function __construct(
        public readonly array $result
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['result']
        );
    }
} 