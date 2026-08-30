<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use Mattsplat\VinDecode\DTO\Manufacturer;
use Mattsplat\VinDecode\DTO\ManufacturerDetail;
use Mattsplat\VinDecode\DTO\Wmi;
use Mattsplat\VinDecode\DTO\WmiManufacturer;

final class ManufacturersAndWmiTest extends TestCase
{
    public function test_all_manufacturers_paginates(): void
    {
        $response = $this->fakeVpic('all_manufacturers')->allManufacturers(page: 2);

        $this->assertContainsOnlyInstancesOf(Manufacturer::class, $response->all());
        $mfr = $response->first();
        $this->assertGreaterThan(0, $mfr->id);
        $this->assertNotSame('', $mfr->name);
        $this->assertIsArray($mfr->vehicleTypes);
        $this->assertStringContainsString('page=2', $this->lastRequest()->getUri()->getQuery());
    }

    public function test_manufacturer_details_maps_typed_fields_and_nested_vehicle_types(): void
    {
        $response = $this->fakeVpic('manufacturer_details')->manufacturerDetails(987);

        $detail = $response->first();
        $this->assertInstanceOf(ManufacturerDetail::class, $detail);
        $this->assertSame(987, $detail->id);
        $this->assertSame('HONDA MOTOR CO., LTD.', $detail->name);
        $this->assertContains('Completed Vehicle Manufacturer', $detail->manufacturerTypes);
        $this->assertSame($detail->get('Mfr_Name'), $detail->name);

        $this->assertNotSame([], $detail->vehicleTypes);
        $this->assertIsBool($detail->vehicleTypes[0]->isPrimary);
    }

    public function test_decode_wmi(): void
    {
        $wmi = $this->fakeVpic('decode_wmi')->decodeWmi('1FD');

        $this->assertInstanceOf(Wmi::class, $wmi);
        $this->assertSame('FORD', $wmi->make);
        $this->assertSame('FORD MOTOR COMPANY', $wmi->manufacturerName);
        $this->assertSame('Incomplete Vehicle', $wmi->vehicleType);
    }

    public function test_wmis_for_manufacturer(): void
    {
        $response = $this->fakeVpic('wmis_for_manufacturer')
            ->wmisForManufacturer('hon', 'Motorcycle');

        $this->assertContainsOnlyInstancesOf(WmiManufacturer::class, $response->all());
        $first = $response->first();
        $this->assertNotSame('', $first->wmi);
        $this->assertGreaterThan(0, $first->manufacturerId);
        $this->assertStringContainsString('vehicleType=Motorcycle', $this->lastRequest()->getUri()->getQuery());
    }
}
