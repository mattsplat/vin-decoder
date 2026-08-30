<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use Mattsplat\VinDecode\DTO\VinResult;
use Mattsplat\VinDecode\DTO\VinVariable;
use Mattsplat\VinDecode\Exceptions\VpicException;

final class VinDecodingTest extends TestCase
{
    public function test_decode_vin_returns_variable_rows(): void
    {
        $response = $this->fakeVpic('decode_vin')->decodeVin('5UXWX7C5*BA', 2011);

        $this->assertContainsOnlyInstancesOf(VinVariable::class, $response->all());

        $make = $this->firstWhere($response->all(), 'Make');
        $this->assertSame('BMW', $make->value);
        $this->assertSame(26, $make->variableId);

        $this->assertStringContainsString('modelyear=2011', $this->lastRequest()->getUri()->getQuery());
    }

    public function test_decode_vin_flat_returns_a_single_result(): void
    {
        $result = $this->fakeVpic('decode_vin_values')->decodeVinFlat('5UXWX7C5*BA', 2011);

        $this->assertInstanceOf(VinResult::class, $result);
        $this->assertSame('BMW', $result->make());
        $this->assertSame('X3', $result->model());
        $this->assertSame(2011, $result->modelYear());
        $this->assertSame('Sport Utility Vehicle [SUV]/Multipurpose Vehicle [MPV]', $result->bodyClass());
        $this->assertSame(6, $result->engineCylinders());
    }

    public function test_vin_result_exposes_unmapped_fields_and_array_access(): void
    {
        $result = $this->fakeVpic('decode_vin_values')->decodeVinFlat('5UXWX7C5*BA');

        $this->assertSame('MUNICH', $result->get('PlantCity'));
        $this->assertSame('MUNICH', $result['PlantCity']);
        $this->assertNull($result->get('BatteryType'));
        $this->assertFalse($result->has('BatteryType'));
    }

    public function test_extended_decoders_hit_the_extended_endpoints(): void
    {
        $vpic = $this->fakeVpic('decode_vin_extended', 'decode_vin_values_extended');

        $vpic->decodeVinExtended('1abc');
        $this->assertStringContainsString('DecodeVinExtended', (string) $this->lastRequest()->getUri());

        $vpic->decodeVinFlatExtended('1abc');
        $this->assertStringContainsString('DecodeVinValuesExtended', (string) $this->lastRequest()->getUri());
    }

    public function test_decode_vin_batch_builds_the_data_payload(): void
    {
        $vpic = $this->fakeVpic('decode_vin_batch');

        $response = $vpic->decodeVinBatch([['5UXWX7C5*BA', 2011], '5YJSA3DS*EF']);

        $this->assertContainsOnlyInstancesOf(VinResult::class, $response->all());
        $this->assertCount(2, $response);

        $body = (string) $this->lastRequest()->getBody();
        parse_str($body, $parsed);
        $this->assertSame('5UXWX7C5*BA,2011;5YJSA3DS*EF', $parsed['data']);
    }

    public function test_decode_vin_batch_rejects_more_than_fifty_vins(): void
    {
        $this->expectException(VpicException::class);

        $this->fakeVpic('decode_vin_batch')->decodeVinBatch(array_fill(0, 51, '1abc'));
    }

    public function test_decode_vin_batch_rejects_an_empty_list(): void
    {
        $this->expectException(VpicException::class);

        $this->fakeVpic('decode_vin_batch')->decodeVinBatch([]);
    }

    /**
     * @param  list<VinVariable>  $rows
     */
    private function firstWhere(array $rows, string $variable): VinVariable
    {
        foreach ($rows as $row) {
            if ($row->variable === $variable) {
                return $row;
            }
        }

        $this->fail("No '{$variable}' row in response");
    }
}
