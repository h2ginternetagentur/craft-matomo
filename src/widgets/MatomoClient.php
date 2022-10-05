<?php

namespace h2g\matomo\widgets;

use Craft;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class MatomoClient
{
    const MATOMO_ENDPOINT_URI = '/index.php';

    private Client $client;
    private array $defaultParams;

    public function __construct()
    {
        $settings = \h2g\matomo\Matomo::getInstance()->settings;

        $this->defaultParams = [
            'idSite' => $settings->siteId,
            'token_auth' => $settings->authToken,
            'format' => 'JSON',
            'language' => substr(Craft::$app->locale, 0, 2),
        ];

        $configs = [
            'base_uri' => $settings->matomoUrl,
            'header' => [
                'Authorized' => 'Bearer ' . $settings->authToken
            ]
        ];

        $this->client = new Client($configs);
    }

    public function getWithParams(array $parameters): array
    {
        try {
            $response = $this->client->request('GET', self::MATOMO_ENDPOINT_URI, ['query' => array_merge($this->defaultParams, $parameters)]);
            return json_decode($response->getBody()->getContents(), true);
        } catch(GuzzleException $exception) {
            dd($exception);
            //TDOD log error
            return [];
        }
    }
}