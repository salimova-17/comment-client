<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class CommentClient
 * @package App
 */
class CommentClient
{
    protected Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    protected function getBaseUri(): string
    {
        return 'https://example.com/api/v1';
    }

    /**
     * @return mixed
     * @throws \JsonException
     */
    public function getComments(): mixed
    {
        $uri = $this->getBaseUri() . '/comments';
        try {
            $response = $this->httpClient->get($uri);
            if ($response->getStatusCode() !== 200 || !$response->getBody()->getSize()) {
                return null;
            }
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $text
     * @return mixed
     * @throws \JsonException
     */
    public function addComment($name, $text): mixed
    {
        $uri = $this->getBaseUri() . '/comment';
        try {
            $response = $this->httpClient->post($uri, [
                'form_params' => [
                    'name' => $name,
                    'text' => $text,
                ]
            ]);
            if ($response->getStatusCode() !== 200 || !$response->getBody()->getSize()) {
                return null;
            }
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $text
     * @return mixed
     * @throws \JsonException
     */
    public function editComment($id, $name, $text): mixed
    {
        $uri = $this->getBaseUri() . '/comment/' . $id;
        try {
            $response = $this->httpClient->put($uri, [
                'form_params' => [
                    'name' => $name,
                    'text' => $text,
                ]
            ]);
            if ($response->getStatusCode() !== 200 || !$response->getBody()->getSize()) {
                return null;
            }
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            return null;
        }
    }
}