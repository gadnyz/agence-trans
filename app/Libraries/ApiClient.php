<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

/**
 * @deprecated V1 monolithique : ne plus utiliser de loopback HTTP vers /api.
 * Les controllers Web doivent appeler les modèles / services directement.
 * Conservé temporairement pour scripts Temp/ uniquement.
 */
class ApiClient
{
    protected CURLRequest $client;
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(base_url(), '/') . '/api/';

        $options = [
            'baseURI' => $this->baseUrl,
            'timeout' => 10,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $this->client = Services::curlrequest($options, null, null, false);
    }

    public function get(string $uri, array $query = []): ?array
    {
        $options = [];
        if (!empty($query)) {
            $options['query'] = $query;
        }

        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $json = []): ?array
    {
        $options = [];
        if (!empty($json)) {
            $options['json'] = $json;
        }

        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $json = []): ?array
    {
        $options = [];
        if (!empty($json)) {
            $options['json'] = $json;
        }

        return $this->request('PUT', $uri, $options);
    }

    public function delete(string $uri): ?array
    {
        return $this->request('DELETE', $uri);
    }

    protected function request(string $method, string $uri, array $options = []): ?array
    {
        log_message('warning', '[ApiClient] Deprecated loopback call to ' . $uri);

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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            log_message('error', '[ApiClient] Refresh failed: ' . $e->getMessage());

            return false;
        }
    }
}
