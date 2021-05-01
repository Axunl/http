<?php


namespace axunl\httpClient;

use axunl\httpClient\implement\GuzzleHttpClient;
use axunl\httpClient\implement\SimpleHttpClient;

/**
 * Class Container
 * @package axunl\httpClient
 * @property SimpleHttpClient $simple
 * @property GuzzleHttpClient $guzzle
 */
class Container extends \axunl\container\Container
{
    protected $base = [
        'simple' => SimpleHttpClient::class,
        'guzzle' => GuzzleHttpClient::class
    ];
}