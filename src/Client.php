<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Mattsplat\VinDecode\Exceptions\VpicRequestException;

/**
 * Thin HTTP wrapper around the NHTSA vPIC API.
 *
 * Every request forces `format=json`; responses are returned as the decoded
 * associative array (the raw `{Count, Message, SearchCriteria, Results}` envelope).
 */
class Client
{
    public const BASE_URI = 'https://vpic.nhtsa.dot.gov/api/';

    private ClientInterface $http;

    public function __construct(
        ?ClientInterface $http = null,
        private readonly string $baseUri = self::BASE_URI,
    ) {
        $this->http = $http ?? new GuzzleClient([
            'timeout' => 30,
            'headers' => ['Accept' => 'application/json'],
        ]);
    }

    /**
     * Perform a GET request against a vPIC endpoint.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $this->normalize($query)]);
    }

    /**
     * Perform a POST request against a vPIC endpoint (used by the batch decoder).
     *
     * @param  array<string, mixed>  $form
     * @return array<string, mixed>
     */
    public function post(string $path, array $form = []): array
    {
        return $this->request('POST', $path, ['form_params' => $this->normalize($form)]);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $options): array
    {
        $path = ltrim($path, '/');
        $options['query'] = array_merge($options['query'] ?? [], ['format' => 'json']);

        try {
            $response = $this->http->request($method, $this->baseUri.$path, $options);
        } catch (GuzzleException $e) {
            throw new VpicRequestException(
                sprintf('Request to vPIC endpoint "%s" failed: %s', $path, $e->getMessage()),
                $path,
                $e,
            );
        }

        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);

        if (! is_array($decoded)) {
            throw new VpicRequestException(
                sprintf('vPIC endpoint "%s" returned a response that is not valid JSON.', $path),
                $path,
            );
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * Drop null values and cast booleans to the strings vPIC expects.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, scalar>
     */
    private function normalize(array $params): array
    {
        $normalized = [];

        foreach ($params as $key => $value) {
            if ($value === null) {
                continue;
            }

            $normalized[$key] = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        }

        return $normalized;
    }
}
