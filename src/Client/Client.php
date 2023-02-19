<?php

declare(strict_types=1);

namespace WS\Client;

use GuzzleHttp\Client as HttpClient;

class Client
{
    private $baseUrl;
    private $apiKey;
    private $timeout = 90;

    public function __construct(
        string $baseUrl,
        string $apiKey
    ) {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    public function getClient(): HttpClient
    {
        $headers = [
            'Authorization' => 'Api-Key ' . $this->apiKey,
            'Accept' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36'
        ];

        return new HttpClient([
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'headers' => $headers,
        ]);
    }
}