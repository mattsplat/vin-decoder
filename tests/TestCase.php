<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Mattsplat\VinDecode\Client;
use Mattsplat\VinDecode\Vpic;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @var list<Request> */
    protected array $recordedRequests = [];

    /**
     * Build a Vpic instance whose HTTP layer replays the given canned responses.
     *
     * @param  string|array<string, mixed>  ...$responses  fixture name, raw JSON, or a payload array
     */
    protected function fakeVpic(string|array ...$responses): Vpic
    {
        return new Vpic($this->fakeClient(...$responses));
    }

    /**
     * @param  string|array<string, mixed>  ...$responses
     */
    protected function fakeClient(string|array ...$responses): Client
    {
        $queue = [];

        foreach ($responses as $response) {
            $body = match (true) {
                is_array($response) => json_encode($response, JSON_THROW_ON_ERROR),
                str_starts_with(ltrim($response), '{') => $response,
                default => $this->fixture($response),
            };

            $queue[] = new PsrResponse(200, ['Content-Type' => 'application/json'], $body);
        }

        $this->recordedRequests = [];
        $stack = HandlerStack::create(new MockHandler($queue));
        $stack->push(Middleware::history($this->recordedRequests));

        return new Client(new GuzzleClient(['handler' => $stack]));
    }

    protected function fixture(string $name): string
    {
        $path = __DIR__.'/fixtures/'.$name.'.json';

        if (! is_file($path)) {
            $this->fail("Missing fixture: {$name}.json");
        }

        return (string) file_get_contents($path);
    }

    protected function lastRequest(): Request
    {
        $entry = end($this->recordedRequests);
        $this->assertNotFalse($entry, 'No HTTP request was recorded.');

        return $entry['request'];
    }
}
