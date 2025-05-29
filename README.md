# Seeru Flight SDK for PHP

A modern PHP 8.0+ SDK for the Seeru Travel Flight API.

## Installation

```bash
composer require seeru/flight-sdk
```

## Requirements

- PHP 8.0 or higher
- ext-curl
- ext-json

## Usage

### Initialize the SDK

```php
use Seeru\FlightSDK\SeeruFlightSDK;

$sdk = new SeeruFlightSDK('your_api_token');
```

### Search for Flights

```php
use Seeru\FlightSDK\Exceptions\SeeruApiException;

try {
    $searchResponse = $sdk->search(
        fromAirport: 'CAI',
        toAirport: 'JED',
        date: '20250622',
        adt: 1,
        chd: 0,
        inf: 0
    );

    $searchId = $searchResponse->searchId;
    
    // Get search results
    $searchResult = $sdk->getSearchResult($searchId);
    $flights = $searchResult->result;
} catch (SeeruApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Book a Flight

```php
use Seeru\FlightSDK\Types\{Contact, Passenger};

try {
    // Check fare
    $fareResponse = $sdk->bookingFare($flights[0]);
    
    if ($fareResponse->status === 'ok') {
        // Create passenger
        $passenger = new Passenger(
            type: 'ADT',
            firstName: 'John',
            lastName: 'Doe',
            gender: 'M',
            birthDate: '1990-01-01',
            documentType: 'PP',
            documentNumber: 'A1234567',
            documentExpiry: '2025-01-01',
            documentCountry: 'USA',
            nationality: 'USA'
        );

        // Create contact
        $contact = new Contact(
            fullName: 'John Doe',
            email: 'john@example.com',
            mobile: '1234567890'
        );

        // Save booking
        $bookingResponse = $sdk->bookingSave(
            bookingData: $fareResponse->booking,
            passengers: [$passenger],
            contact: $contact
        );

        $orderId = $bookingResponse->orderId;
    }
} catch (SeeruApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Manage Orders

```php
try {
    // Get order details
    $orderDetails = $sdk->getOrderDetails($orderId);

    // Issue order
    $issuedOrder = $sdk->issueOrder($orderId);

    // Cancel order
    $cancelledOrder = $sdk->cancelOrder($orderId);
} catch (SeeruApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Error Handling

The SDK throws `SeeruApiException` for any API errors. The exception includes:
- Error message
- HTTP status code (when applicable)

## Features

- Modern PHP 8.0+ features (named arguments, readonly properties)
- Strong typing with type declarations
- Proper error handling
- Immutable response objects
- PSR-4 autoloading
- Easy to use fluent interface

## License

MIT License 