<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use Mattsplat\VinDecode\Exceptions\VpicException;
use Mattsplat\VinDecode\Responses\Response;

final class ResponseTest extends TestCase
{
    public function test_it_exposes_the_envelope(): void
    {
        $response = $this->fakeVpic('models_for_make')->modelsForMake('honda');

        $this->assertSame(362, $response->reportedCount);
        $this->assertStringContainsString('successfully', $response->message);
        $this->assertSame('Make:honda', $response->searchCriteria);
        $this->assertArrayHasKey('Results', $response->raw);
    }

    public function test_it_is_iterable_and_countable(): void
    {
        $response = $this->fakeVpic('models_for_make')->modelsForMake('honda');

        $this->assertCount(12, $response);
        $this->assertSame($response->all()[0], $response[0]);

        $seen = 0;
        foreach ($response as $model) {
            $this->assertNotSame('', $model->name);
            $seen++;
        }
        $this->assertSame(12, $seen);
    }

    public function test_empty_payload_is_handled(): void
    {
        $response = $this->fakeVpic(['Count' => 0, 'Message' => 'none', 'Results' => []])
            ->modelsForMake('nope');

        $this->assertTrue($response->isEmpty());
        $this->assertNull($response->first());
        $this->assertSame(0, $response->count());
    }

    public function test_it_is_immutable(): void
    {
        $response = $this->fakeVpic('models_for_make')->modelsForMake('honda');

        $this->expectException(VpicException::class);
        $response[0] = 'nope';
    }

    public function test_from_payload_defaults_count_to_row_count_when_absent(): void
    {
        $response = Response::fromPayload(
            ['Results' => [[], []]],
            static fn (array $row): array => $row,
        );

        $this->assertSame(2, $response->reportedCount);
        $this->assertNull($response->searchCriteria);
    }
}
