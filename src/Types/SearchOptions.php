<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class SearchOptions
{
    public const CABIN_ECONOMY = 'e';
    public const CABIN_PREMIUM_ECONOMY = 'p';
    public const CABIN_BUSINESS = 'b';
    public const CABIN_FIRST = 'f';

    public function __construct(
        public readonly ?string $airline = null,
        public readonly ?string $source = null,
        public readonly bool $direct = false,
        public readonly string $cabin = self::CABIN_ECONOMY
    ) {
        $this->validateOptions();
    }

    private function validateOptions(): void
    {
        if ($this->airline !== null && !preg_match('/^[A-Z0-9]+(,[A-Z0-9]+)*$/', $this->airline)) {
            throw new \InvalidArgumentException('Airline must be comma-separated IATA codes');
        }

        if ($this->source !== null && !preg_match('/^[A-Z]+(,[A-Z]+)*$/', $this->source)) {
            throw new \InvalidArgumentException('Source must be comma-separated 2-char uppercase letters');
        }

        if (!in_array($this->cabin, [self::CABIN_ECONOMY, self::CABIN_PREMIUM_ECONOMY, self::CABIN_BUSINESS, self::CABIN_FIRST])) {
            throw new \InvalidArgumentException('Invalid cabin class');
        }
    }

    public function toQueryString(): string
    {
        $options = [];
        
        if ($this->airline !== null) {
            $options[] = "airline={$this->airline}";
        }
        
        if ($this->source !== null) {
            $options[] = "source={$this->source}";
        }
        
        if ($this->direct) {
            $options[] = "direct=1";
        }
        
        if ($this->cabin !== self::CABIN_ECONOMY) {
            $options[] = "cabin={$this->cabin}";
        }

        return empty($options) ? '' : '?' . implode('&', $options);
    }

    public static function fromArray(array $options): self
    {
        return new self(
            airline: $options['airline'] ?? null,
            source: $options['source'] ?? null,
            direct: (bool)($options['direct'] ?? false),
            cabin: $options['cabin'] ?? self::CABIN_ECONOMY
        );
    }
} 