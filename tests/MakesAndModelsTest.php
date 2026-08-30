<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use Mattsplat\VinDecode\DTO\Make;
use Mattsplat\VinDecode\DTO\Model;

final class MakesAndModelsTest extends TestCase
{
    public function test_all_makes(): void
    {
        $response = $this->fakeVpic('all_makes')->allMakes();

        $this->assertContainsOnlyInstancesOf(Make::class, $response->all());
        $first = $response->first();
        $this->assertNotNull($first);
        $this->assertGreaterThan(0, $first->id);
        $this->assertNotSame('', $first->name);
        $this->assertSame(12351, $response->reportedCount);
    }

    public function test_makes_for_manufacturer_carries_the_manufacturer_name(): void
    {
        $response = $this->fakeVpic('makes_for_manufacturer')->makesForManufacturer('honda');

        $make = $response->first();
        $this->assertSame(474, $make->id);
        $this->assertSame('HONDA', $make->name);
        $this->assertSame('HONDA MOTOR CO., LTD.', $make->manufacturerName);
        $this->assertStringContainsString('GetMakeForManufacturer/honda', (string) $this->lastRequest()->getUri());
    }

    public function test_makes_for_manufacturer_and_year_sends_the_year(): void
    {
        $response = $this->fakeVpic('makes_for_manufacturer_year')
            ->makesForManufacturerAndYear(987, 2020);

        $this->assertSame(474, $response->first()->id);
        $this->assertSame(987, $response->first()->manufacturerId);
        $this->assertStringContainsString('year=2020', $this->lastRequest()->getUri()->getQuery());
    }

    public function test_makes_for_vehicle_type(): void
    {
        $response = $this->fakeVpic('makes_for_vehicle_type')->makesForVehicleType('car');

        $make = $response->first();
        $this->assertSame(2, $make->vehicleTypeId);
        $this->assertSame('Passenger Car', $make->vehicleTypeName);
    }

    public function test_models_for_make(): void
    {
        $response = $this->fakeVpic('models_for_make')->modelsForMake('honda');

        $this->assertContainsOnlyInstancesOf(Model::class, $response->all());
        $accord = $response->first();
        $this->assertSame('Accord', $accord->name);
        $this->assertSame(474, $accord->makeId);
        $this->assertSame('Honda', $accord->makeName);
    }

    public function test_models_for_make_id_builds_the_path(): void
    {
        $this->fakeVpic('models_for_make_id')->modelsForMakeId(474);

        $this->assertStringContainsString('GetModelsForMakeId/474', (string) $this->lastRequest()->getUri());
    }

    public function test_models_for_make_year_with_vehicle_type_segment(): void
    {
        $this->fakeVpic('models_for_make_year')->modelsForMakeYear('honda', 2015, 'truck');

        $path = $this->lastRequest()->getUri()->getPath();
        $this->assertStringContainsString('/make/honda/modelyear/2015/vehicleType/truck', $path);
    }

    public function test_models_for_make_id_year_without_vehicle_type(): void
    {
        $this->fakeVpic('models_for_make_year')->modelsForMakeIdYear(474, 2015);

        $path = $this->lastRequest()->getUri()->getPath();
        $this->assertStringContainsString('/makeId/474/modelyear/2015', $path);
        $this->assertStringNotContainsString('vehicleType', $path);
    }
}
