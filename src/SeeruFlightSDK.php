<?php

declare(strict_types=1);

namespace Seeru\FlightSDK;

use Seeru\FlightSDK\Exceptions\SeeruApiException;
use Seeru\FlightSDK\Types\{
    SearchResponse,
    SearchResultResponse,
    BookingFareResponse,
    BookingSaveResponse,
    OrderResponse,
    Passenger,
    Contact,
    SearchOptions
};

class SeeruFlightSDK
{
    private const API_BASE_URL = 'https://sandbox-api.seeru.travel/v1/flights';

    public function __construct(
        private readonly string $apiToken,
        private readonly ?string $baseUrl = self::API_BASE_URL
    ) {}

    /**
     * Search for one-way flights (Legacy method)
     * 
     * @param string $fromAirport Origin airport code (e.g., CAI)
     * @param string $toAirport Destination airport code (e.g., JED)
     * @param string $date Flight date in YYYYMMDD format
     * @param int $adt Number of adults
     * @param int $chd Number of children
     * @param int $inf Number of infants
     * @param array|SearchOptions|null $options Search options
     * @return SearchResponse
     * @throws SeeruApiException
     */
    public function search(
        string $fromAirport,
        string $toAirport,
        string $date,
        int $adt = 1,
        int $chd = 0,
        int $inf = 0,
        array|SearchOptions|null $options = null
    ): SearchResponse {
        $route = "{$fromAirport}-{$toAirport}-{$date}";
        return $this->searchMultiCity([$route], $adt, $chd, $inf, $options);
    }

    /**
     * Search for round-trip flights
     * 
     * @param string $fromAirport Origin airport code (e.g., CAI)
     * @param string $toAirport Destination airport code (e.g., JED)
     * @param string $departDate Departure date in YYYYMMDD format
     * @param string $returnDate Return date in YYYYMMDD format
     * @param int $adt Number of adults
     * @param int $chd Number of children
     * @param int $inf Number of infants
     * @param array|SearchOptions|null $options Search options
     * @return SearchResponse
     * @throws SeeruApiException
     */
    public function searchRoundTrip(
        string $fromAirport,
        string $toAirport,
        string $departDate,
        string $returnDate,
        int $adt = 1,
        int $chd = 0,
        int $inf = 0,
        array|SearchOptions|null $options = null
    ): SearchResponse {
        $routes = [
            "{$fromAirport}-{$toAirport}-{$departDate}",
            "{$toAirport}-{$fromAirport}-{$returnDate}"
        ];
        
        return $this->searchMultiCity($routes, $adt, $chd, $inf, $options);
    }

    /**
     * Search for multi-city flights
     * This method can be used for one-way, round-trip, or multi-city searches
     * 
     * @param array $trips Array of trip segments in format ["FROM-TO-DATE", ...]
     *                     or ["FROM", "TO", "DATE"] for each segment
     * @param int $adt Number of adults
     * @param int $chd Number of children
     * @param int $inf Number of infants
     * @param array|SearchOptions|null $options Search options
     * @return SearchResponse
     * @throws SeeruApiException
     */
    public function searchMultiCity(
        array $trips,
        int $adt = 1,
        int $chd = 0,
        int $inf = 0,
        array|SearchOptions|null $options = null
    ): SearchResponse {
        // Process trips array to handle both string format and array format
        $processedTrips = array_map(function($trip) {
            if (is_array($trip)) {
                if (count($trip) !== 3) {
                    throw new SeeruApiException('Each trip array must contain exactly 3 elements: [fromAirport, toAirport, date]');
                }
                return implode('-', $trip);
            }
            return $trip;
        }, $trips);

        // Join all trips with colon
        $route = implode(':', $processedTrips);
        
        // Process search options
        if (is_array($options)) {
            $options = SearchOptions::fromArray($options);
        }
        
        $endpoint = "/search/{$route}/{$adt}/{$chd}/{$inf}";
        
        if ($options instanceof SearchOptions) {
            $endpoint .= $options->toQueryString();
        }
        
        $response = $this->makeRequest($endpoint);
        return SearchResponse::fromArray($response);
    }

    /**
     * Get search results
     * 
     * @param string $searchId Search ID from previous search request
     * @return SearchResultResponse
     * @throws SeeruApiException
     */
    public function getSearchResult(string $searchId): SearchResultResponse {
        $endpoint = "/result/{$searchId}?wait=1";
        $response = $this->makeRequest($endpoint);
        return SearchResultResponse::fromArray($response);
    }

    /**
     * Check booking fare
     * 
     * @param array $tripData Trip data from search results
     * @return BookingFareResponse
     * @throws SeeruApiException
     */
    public function bookingFare(array $tripData): BookingFareResponse {
        $endpoint = "/booking/fare";
        $response = $this->makeRequest($endpoint, 'POST', ['booking' => $tripData]);
        return BookingFareResponse::fromArray($response);
    }

    /**
     * Save booking
     * 
     * @param array $bookingData Booking data from fare check
     * @param array<Passenger> $passengers List of passengers
     * @param Contact $contact Contact information
     * @return BookingSaveResponse
     * @throws SeeruApiException
     */
    public function bookingSave(
        array $bookingData,
        array $passengers,
        Contact $contact
    ): BookingSaveResponse {
        $endpoint = "/booking/save";
        $data = [
            'booking' => $bookingData,
            'passengers' => array_map(fn(Passenger $p) => $p->toArray(), $passengers),
            'contact' => $contact->toArray()
        ];
        $response = $this->makeRequest($endpoint, 'POST', $data);
        return BookingSaveResponse::fromArray($response);
    }

    /**
     * Get order details
     * 
     * @param string $orderId Order ID
     * @return OrderResponse
     * @throws SeeruApiException
     */
    public function getOrderDetails(string $orderId): OrderResponse {
        $endpoint = "/order/details";
        $response = $this->makeRequest($endpoint, 'POST', ['order_id' => $orderId]);
        return OrderResponse::fromArray($response);
    }

    /**
     * Cancel order
     * 
     * @param string $orderId Order ID
     * @return OrderResponse
     * @throws SeeruApiException
     */
    public function cancelOrder(string $orderId): OrderResponse {
        $endpoint = "/order/cancel";
        $response = $this->makeRequest($endpoint, 'POST', ['order_id' => $orderId]);
        return OrderResponse::fromArray($response);
    }

    /**
     * Issue order
     * 
     * @param string $orderId Order ID
     * @return OrderResponse
     * @throws SeeruApiException
     */
    public function issueOrder(string $orderId): OrderResponse {
        $endpoint = "/order/issue";
        $response = $this->makeRequest($endpoint, 'POST', ['order_id' => $orderId]);
        return OrderResponse::fromArray($response);
    }

    /**
     * Make HTTP request to Seeru API
     * 
     * @param string $endpoint API endpoint
     * @param string $method HTTP method
     * @param array|null $data Request data
     * @return array
     * @throws SeeruApiException
     */
    private function makeRequest(string $endpoint, string $method = 'GET', ?array $data = null): array {
        $curl = curl_init();
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($data && in_array($method, ['POST', 'PUT'])) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data, JSON_THROW_ON_ERROR);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            throw new SeeruApiException('cURL Error: ' . $err);
        }

        $responseData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if ($statusCode >= 400) {
            throw new SeeruApiException(
                $responseData['error'] ?? 'Unknown error',
                $statusCode
            );
        }

        return $responseData;
    }
} 