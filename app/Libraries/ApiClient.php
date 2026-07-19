<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class ApiClient
{
    protected CURLRequest $client;
    protected string $baseUrl;

    public function __construct()
    {
        // Use the base URL of the application, appending /api if needed
        // Assuming API routes are prefixed with /api
        $this->baseUrl = rtrim(base_url(), '/') . '/api/';

        $options = [
            'baseURI' => $this->baseUrl,
            'timeout' => 10,
            'http_errors' => false, // Do not throw exception on 4xx/5xx errors
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $this->client = Services::curlrequest($options, null, null, false);
    }

    /**
     * Performs a GET request
     * 
     * @param string $uri The URI to append to the base URI
     * @param array $query Query parameters
     * @return array|null Returns decoded JSON or null on failure
     */
    public function get(string $uri, array $query = []): ?array
    {
        $options = [];
        if (!empty($query)) {
            $options['query'] = $query;
        }

        return $this->request('GET', $uri, $options);
    }

    /**
     * Performs a POST request
     *
     * @param string $uri
     * @param array $json Data to be sent as JSON
     * @return array|null
     */
    public function post(string $uri, array $json = []): ?array
    {
        $options = [];
        if (!empty($json)) {
            $options['json'] = $json;
        }

        return $this->request('POST', $uri, $options);
    }

    /**
     * Performs a PUT request
     *
     * @param string $uri
     * @param array $json
     * @return array|null
     */
    public function put(string $uri, array $json = []): ?array
    {
        $options = [];
        if (!empty($json)) {
            $options['json'] = $json;
        }

        return $this->request('PUT', $uri, $options);
    }

    /**
     * Performs a DELETE request
     *
     * @param string $uri
     * @return array|null
     */
    public function delete(string $uri): ?array
    {
        return $this->request('DELETE', $uri);
    }

    /**
     * Core request method (Interceptor)
     */
    protected function request(string $method, string $uri, array $options = []): ?array
    {
        try {
            $this->attachAuthorization($uri);
            $response = $this->client->request($method, $uri, $options);

            if ($response->getStatusCode() === 401 && $this->refreshSession()) {
                $this->attachAuthorization($uri);
                $response = $this->client->request($method, $uri, $options);
            }

            $body = $response->getBody();
            
            if (empty($body)) {
                return null;
            }
            
            return json_decode($body, true);
        } catch (\Throwable $e) {
            log_message('error', '[ApiClient] Request failed: ' . $e->getMessage());
            return null;
        }
    }

    private function attachAuthorization(string $uri): void
    {
        $exemptRoutes = ['auth/login', 'auth/refresh'];

        if (in_array($uri, $exemptRoutes, true)) {
            return;
        }

        $token = session()->get('access_token');

        if ($token) {
            $this->client->setHeader('Authorization', 'Bearer ' . $token);
        }
    }

    private function refreshSession(): bool
    {
        $refreshToken = session()->get('refresh_token');

        if (! $refreshToken) {
            return false;
        }

        try {
            $response = $this->client->request('POST', 'auth/refresh', [
                'json' => [
                    'refresh_token' => $refreshToken,
                ],
            ]);
            $payload = json_decode($response->getBody(), true);

            if (
                $response->getStatusCode() !== 200
                || ! is_array($payload)
                || ($payload['success'] ?? false) !== true
                || empty($payload['data']['token']['access_token'])
                || empty($payload['data']['token']['refresh_token'])
            ) {
                return false;
            }

            session()->set('access_token', $payload['data']['token']['access_token']);
            session()->set('refresh_token', $payload['data']['token']['refresh_token']);

            if (isset($payload['data']['user'])) {
                session()->set('user', $payload['data']['user']);
            }

            return true;
        } catch (\Throwable $e) {
            log_message('error', '[ApiClient] Refresh failed: ' . $e->getMessage());

            return false;
        }
    }
}
