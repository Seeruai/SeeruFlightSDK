<?php

require_once __DIR__ . '/vendor/autoload.php';

use Seeru\FlightSDK\SeeruFlightSDK;
use Seeru\FlightSDK\Types\{Contact, Passenger, SearchOptions};
use Seeru\FlightSDK\Exceptions\SeeruApiException;

// Initialize the SDK with your API token
$apiToken = 'your_api_token_here'; // Replace with your actual API token
$sdk = new SeeruFlightSDK($apiToken);

try {
    echo "1. Searching for flights with options...\n";
    
    // Create search options
    $options = new SearchOptions(
        airline: 'EK,EY',      // Filter for Emirates and Etihad flights
        source: 'NS,MW',       // Search in specific sources
        direct: true,          // Only direct flights
        cabin: SearchOptions::CABIN_BUSINESS // Business class
    );

    // Search with options
    $searchResponse = $sdk->search(
        fromAirport: 'JED',
        toAirport: 'DXB',
        date: '20250622',
        adt: 1,
        chd: 0,
        inf: 0,
        options: $options
    );

    echo "Search ID: {$searchResponse->searchId}\n";
    echo "Search options: Direct flights only, Business class, Airlines: EK,EY\n";

    echo "\n2. Getting search results...\n";
    $searchResult = $sdk->getSearchResult($searchResponse->searchId);
    
    if (empty($searchResult->result)) {
        throw new Exception("No flights found! Try broadening your search criteria.");
    }

    $selectedFlight = $searchResult->result[0];
    echo "Found " . count($searchResult->result) . " flights\n";
    echo "Selected flight price: $" . ($selectedFlight['price'] ?? 'N/A') . "\n";
    echo "Selected flight airline: " . ($selectedFlight['airline'] ?? 'N/A') . "\n";
    echo "Selected flight cabin: " . ($selectedFlight['cabin'] ?? 'N/A') . "\n";

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
        echo "\n7. Example of round-trip search...\n";
        $roundTripOptions = new SearchOptions(
            direct: true,
            cabin: SearchOptions::CABIN_ECONOMY
        );
        
        $roundTripResponse = $sdk->searchRoundTrip(
            fromAirport: 'JED',
            toAirport: 'DXB',
            departDate: '20250622',
            returnDate: '20250629',
            adt: 1,
            options: $roundTripOptions
        );
        
        echo "Round-trip search ID: {$roundTripResponse->searchId}\n";

        echo "\n8. Example of multi-city search...\n";
        $multiCityResponse = $sdk->searchMultiCity([
            'JED-DXB-20250622',
            'DXB-MCT-20250624',
            'MCT-JED-20250626'
        ], options: $options);
        
        echo "Multi-city search ID: {$multiCityResponse->searchId}\n";
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