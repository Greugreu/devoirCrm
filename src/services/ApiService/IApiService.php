<?php

namespace App\services\ApiService;

interface IApiService
{
    function getApiData(string $type, string $content , int $userId);
}