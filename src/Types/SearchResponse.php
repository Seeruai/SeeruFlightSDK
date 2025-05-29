<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class SearchResponse
{
    public function __construct(
        public readonly string $searchId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['search_id']
        );
    }
} 