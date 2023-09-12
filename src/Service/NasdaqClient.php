<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\ItemInterface;

class
NasdaqClient
{
    private HttpClientInterface $httpClient;
    private CacheInterface $cache;

    public function __construct(HttpClientInterface $httpClient, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache      = $cache;
    }

    public function getCompanies(): array
    {
        $response = $this->httpClient->request('GET', $this->getUrl());

        $content = $response->toArray();

        return array_map(function ($item) {
            return [
                'name'   => $item['Company Name'],
                'symbol' => $item['Symbol'],
            ];
        }, $content);
    }

    public function getCachedCompanies()
    {
        $cacheKey = 'nasdaq_listing';

        return $this->cache->get($cacheKey, function (ItemInterface $item) {
            $companies = $this->getCompanies();
            $item->expiresAfter(3600);
            return $companies;
        });
    }

    private function getUrl(): string
    {
        return 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';
    }
}