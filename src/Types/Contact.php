<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class Contact
{
    public function __construct(
        public readonly string $fullName,
        public readonly string $email,
        public readonly string $mobile
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['full_name'],
            $data['email'],
            $data['mobile']
        );
    }

    public function toArray(): array
    {
        return [
            'full_name' => $this->fullName,
            'email' => $this->email,
            'mobile' => $this->mobile
        ];
    }
} 