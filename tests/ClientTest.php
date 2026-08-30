<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Mattsplat\VinDecode\Client;
use Mattsplat\VinDecode\Exceptions\VpicRequestException;

final class ClientTest extends TestCase
{
    public function test_it_forces_json_format_and_targets_the_vpic_base_uri(): void
    {
        $vpic = $this->fakeVpic('all_makes');

        $vpic->allMakes();

        $uri = $this->lastRequest()->getUri();
        $this->assertSame('vpic.nhtsa.dot.gov', $uri->getHost());
        $this->assertStringStartsWith('/api/vehicles/GetAllMakes', $uri->getPath());
        $this->assertStringContainsString('format=json', $uri->getQuery());
    }

    public function test_it_drops_null_query_parameters(): void
    {
        $vpic = $this->fakeVpic('decode_vin_values');

        $vpic->decodeVinFlat('1abc');

        $this->assertStringNotContainsString('modelyear', $this->lastRequest()->getUri()->getQuery());
    }

    public function test_it_wraps_transport_errors(): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new PsrResponse(500, [], 'upstream on fire'),
        ]));
        $client = new Client(new GuzzleClient(['handler' => $stack]));

        $this->expectException(VpicRequestException::class);
        $client->get('vehicles/GetAllMakes');
    }

    public function test_it_rejects_non_json_responses(): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new PsrResponse(200, [], '<html>not json</html>'),
        ]));
        $client = new Client(new GuzzleClient(['handler' => $stack]));

        $this->expectException(VpicRequestException::class);
        $client->get('vehicles/GetAllMakes');
    }

    public function test_request_exception_carries_the_endpoint(): void
    {
        $stack = HandlerStack::create(new MockHandler([new PsrResponse(503)]));
        $client = new Client(new GuzzleClient(['handler' => $stack]));

        try {
            $client->get('vehicles/GetAllMakes');
            $this->fail('Expected VpicRequestException');
        } catch (VpicRequestException $e) {
            $this->assertSame('vehicles/GetAllMakes', $e->endpoint());
        }
    }

    public function test_it_can_be_pointed_at_a_custom_base_uri(): void
    {
        $recorded = [];
        $stack = HandlerStack::create(new MockHandler([
            new PsrResponse(200, [], '{"Results":[]}'),
        ]));
        $stack->push(Middleware::history($recorded));

        $client = new Client(new GuzzleClient(['handler' => $stack]), 'https://example.test/api/');
        $client->get('vehicles/GetAllMakes');

        $this->assertSame('example.test', $recorded[0]['request']->getUri()->getHost());
    }
}
