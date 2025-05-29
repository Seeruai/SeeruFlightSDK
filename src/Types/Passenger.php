<?php

declare(strict_types=1);

namespace Seeru\FlightSDK\Types;

class Passenger
{
    public function __construct(
        public readonly string $type,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $gender,
        public readonly string $birthDate,
        public readonly string $documentType,
        public readonly string $documentNumber,
        public readonly string $documentExpiry,
        public readonly string $documentCountry,
        public readonly string $nationality
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['type'],
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['birth_date'],
            $data['document_type'],
            $data['document_number'],
            $data['document_expiry'],
            $data['document_country'],
            $data['nationality']
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'gender' => $this->gender,
            'birth_date' => $this->birthDate,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
            'document_expiry' => $this->documentExpiry,
            'document_country' => $this->documentCountry,
            'nationality' => $this->nationality
        ];
    }
} 