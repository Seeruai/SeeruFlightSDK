# Seeru Flight SDK

A modern PHP 8.0+ SDK for the Seeru Travel Flight API. This SDK provides a clean, type-safe interface for searching and booking flights through the Seeru Travel platform.

## Features

- Modern PHP 8.0+ features
- PSR-4 autoloading
- Strong typing
- Proper error handling
- Immutable response objects
- Support for one-way, round-trip, and multi-city searches
- Flexible search options

## Installation

```bash
composer require seeru/flight-sdk
```

## Basic Usage

```php
use Seeru\FlightSDK\SeeruFlightSDK;
use Seeru\FlightSDK\Types\SearchOptions;

// Initialize the SDK
$sdk = new SeeruFlightSDK('your-api-token');

// Search for one-way flights
$response = $sdk->search(
    fromAirport: 'DEL',
    toAirport: 'MOW',
    date: '20250612',
    adt: 1,
    chd: 0,
    inf: 0
);

// Search with options
$options = new SearchOptions(
    airline: 'EK,EY',  // Emirates and Etihad
    source: 'NS,MW', // Search in GDS and NDC sources
    direct: true,      // Only direct flights
    cabin: SearchOptions::CABIN_BUSINESS // Business class
);

// Round-trip search with options
$response = $sdk->searchRoundTrip(
    fromAirport: 'DEL',
    toAirport: 'MOW',
    departDate: '20250612',
    returnDate: '20250622',
    options: $options
);

// Multi-city search
$response = $sdk->searchMultiCity([
    'DEL-MOW-20250612',
    'LED-DEL-20250622',
    'DEL-DXB-20250625'
]);
```

## Search Options

The SDK supports the following search options:

| Option  | Description | Format | Default |
|---------|-------------|---------|---------|
| airline | Filter by specific airlines | Comma-separated IATA codes (e.g., 'EK,EY') | null |
| source  | Filter by source systems | Comma-separated 2-char codes (e.g., 'GDS,NDC') | null |
| direct  | Show only direct flights | boolean | false |
| cabin   | Cabin class preference | 'e' (Economy), 'p' (Premium Economy), 'b' (Business), 'f' (First) | 'e' |

Options can be provided either as a `SearchOptions` object or as an array:

```php
// Using SearchOptions object
$options = new SearchOptions(
    airline: 'EK,EY',
    cabin: SearchOptions::CABIN_BUSINESS
);

// Using array
$options = [
    'airline' => 'EK,EY',
    'cabin' => 'b'
];
```

## Route Formats

The SDK supports three search types with flexible input formats:

1. One-way: `DEL-MOW-20250612`
2. Round-trip: `DEL-MOW-20250612:MOW-DEL-20250622`
3. Multi-city: `DEL-MOW-20250612:LED-DEL-20250622:DEL-DXB-20250625`

Each route segment can also be provided as an array:
```php
$routes = [
    ['DEL', 'MOW', '20250612'],
    ['MOW', 'DEL', '20250622']
];
```

## Error Handling

The SDK uses custom exceptions for error handling:

```php
use Seeru\FlightSDK\Exceptions\SeeruApiException;

try {
    $response = $sdk->search('DEL', 'MOW', '20250612');
} catch (SeeruApiException $e) {
    echo "API Error: " . $e->getMessage();
    echo "Status Code: " . $e->getCode();
}
```

## Response Objects

All API responses are converted to immutable objects with type-safe properties:

- `SearchResponse`
- `SearchResultResponse`
- `BookingFareResponse`
- `BookingSaveResponse`
- `OrderResponse`

## License

MIT License 