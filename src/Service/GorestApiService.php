<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GorestApiService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getUsers(): array
    {
        $response = $this->client->request('GET', 'https://gorest.co.in/public/v2/users');
        return $response->toArray();
    }
}
