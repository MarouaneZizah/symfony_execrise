<?php

namespace App\Service;

use Exception;
use Carbon\Carbon;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RapidApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        //        private readonly string $rapidApiKey,
        //        private readonly string $rapidApiHost,
        //        private readonly string $rapidApiUrl
    )
    {
    }


    /**
     * @throws Exception
     */
    public function getHistoricalData(string $symbol, string $startDate, string $endDate): array
    {
        $response = $this->httpClient->request('GET', $this->getBaseUrl('stock/v3/get-historical-data'), [
            'query'   => [
                'symbol' => $symbol,
            ],
            'headers' => [
                'x-rapidapi-key'  => "a024ebbc51mshcd02ff2bb7de0f7p1184fdjsn9e87bc93b237",
                'x-rapidapi-host' => "yh-finance.p.rapidapi.com",
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to get historical data from API');
        }

        $prices = $response->toArray()['prices'];

        if (!$prices) {
            return [];
        }

        return $this->formatPrices($prices, $startDate, $endDate);
    }

    protected function formatPrices($prices, $startDate, $endDate): array
    {
        $filteredPrices = array_filter($prices, function ($price) use ($startDate, $endDate) {
            $date = Carbon::parse($price['date'])->format('Y-m-d H:i:s');

            return Carbon::parse($date)->between($startDate, $endDate);
        });

        return array_values(array_map(function ($price) {
            return [
                'date'   => array_key_exists('date', $price) ? Carbon::parse($price['date'])->format('Y-m-d') : null,
                'open'   => array_key_exists('open', $price) ? (float) number_format($price['open'], 2) : null,
                'high'   => array_key_exists('high', $price) ? (float) number_format($price['high'], 2) : null,
                'low'    => array_key_exists('low', $price) ? (float) number_format($price['low'], 2) : null,
                'close'  => array_key_exists('close', $price) ? (float) number_format($price['close'], 2) : null,
                'volume' => array_key_exists('volume', $price) ? $price['volume'] : null,
            ];
        }, $filteredPrices));
    }

    public function getBaseUrl($endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        $baseUrl  = rtrim("https://yh-finance.p.rapidapi.com", '/');

        return "{$baseUrl}/{$endpoint}";
    }
}