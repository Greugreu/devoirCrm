<?php

namespace App\services\ApiService;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService implements IApiService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $clientInterface)
    {
        $this->client = $clientInterface;
    }

    /**
     * @throws TransportExceptionInterface
     */
    function getApiData(string $type, string $content)
    {
        $root = "https://jsonplaceholder.typicode.com/";
        $call = $this->client->request(
            "GET",
            $root . $type . "/1/" . $content
        );

        try {
            return json_decode($call->getContent(), true);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return $e;
        }
    }
}