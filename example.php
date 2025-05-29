<?php

require_once __DIR__ . '/vendor/autoload.php';

use Seeru\FlightSDK\SeeruFlightSDK;
use Seeru\FlightSDK\Types\{Contact, Passenger};
use Seeru\FlightSDK\Exceptions\SeeruApiException;

// Initialize the SDK with your API token
$apiToken = 'your_api_token_here'; // Replace with your actual API token
$sdk = new SeeruFlightSDK($apiToken);

try {
    echo "1. Searching for flights...\n";
    $searchResponse = $sdk->search(
        fromAirport: 'JED',
        toAirport: 'DXB',
        date: '20250622',
        adt: 1,
        chd: 0,
        inf: 0
    );

    echo "Search ID: {$searchResponse->searchId}\n";

    echo "\n2. Getting search results...\n";
    $searchResult = $sdk->getSearchResult($searchResponse->searchId);
    
    if (empty($searchResult->result)) {
        throw new Exception("No flights found!");
    }

    $selectedFlight = $searchResult->result[0];
    echo "Found " . count($searchResult->result) . " flights\n";
    echo "Selected flight price: $" . ($selectedFlight['price'] ?? 'N/A') . "\n";

    echo "\n3. Checking fare...\n";
    $fareResponse = $sdk->bookingFare($selectedFlight);
    
    if ($fareResponse->status === 'ok') {
        echo "Fare check successful!\n";

        echo "\n4. Creating booking...\n";
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

        echo "Booking successful! Order ID: {$bookingResponse->orderId}\n";

        echo "\n5. Getting order details...\n";
        $orderDetails = $sdk->getOrderDetails($bookingResponse->orderId);
        echo "Order details retrieved successfully!\n";

        echo "\n6. Issuing order...\n";
        $issuedOrder = $sdk->issueOrder($bookingResponse->orderId);
        echo "Order issued successfully!\n";

        // Note: In a real application, you might not want to cancel an order right after issuing it
        // This is just for demonstration purposes
        // TODO: Uncomment this when you want to cancel the order
        // echo "\n7. Cancelling order...\n";
        // $cancelledOrder = $sdk->cancelOrder($bookingResponse->orderId);
        // echo "Order cancelled successfully!\n";
    } else {
        echo "Fare check failed with status: {$fareResponse->status}\n";
        if ($fareResponse->error) {
            echo "Error: {$fareResponse->error}\n";
        }
    }

} catch (SeeruApiException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
    echo "Status Code: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
} 