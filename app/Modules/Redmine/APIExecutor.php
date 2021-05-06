<?php


namespace App\Modules\Redmine;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class APIExecutor
{
    private static Client $client;

    private string $apiKey;

    public function __construct()
    {
        self::$client = new Client([
            'base_uri' => config('redmine.base_uri'),
            'headers' => [
//                'X-Redmine-API-Key' => $this->getApiKey(),
                'X-Redmine-API-Key' => '50552de0e7f743cc8b8387e10f749fb253aabfd3',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function getClient(): Client
    {
        return self::$client;
    }

    /**
     * @param ApiRequest $apiRequest
     * @param ApiCallback $callback
     *
     * @return mixed|null
     */
    public function exec(ApiRequest $apiRequest, ApiCallback $callback)
    {
        try {
            $response = $this->getClient()->request(
                'GET',
                sprintf('/%s.json', $apiRequest->getMethod()),
                [
                    'query' => $apiRequest->getParams()
                ]
            );


            if ($response->getStatusCode() === Response::HTTP_OK) {
                return $callback->onSuccess(json_decode($response->getBody(), true));
            }
        } catch (GuzzleException $e) {
            $callback->onFailed($e);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}
