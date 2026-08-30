<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use Mattsplat\VinDecode\DTO\CanadianSpecification;
use Mattsplat\VinDecode\DTO\Part;
use Mattsplat\VinDecode\DTO\PlantCode;
use Mattsplat\VinDecode\DTO\Variable;
use Mattsplat\VinDecode\DTO\VariableValue;
use Mattsplat\VinDecode\DTO\VehicleType;

final class LookupsTest extends TestCase
{
    public function test_vehicle_types_for_make(): void
    {
        $response = $this->fakeVpic('vehicle_types_for_make')->vehicleTypesForMake('mercedes');

        $this->assertContainsOnlyInstancesOf(VehicleType::class, $response->all());
        $type = $response->first();
        $this->assertSame(2, $type->id);
        $this->assertSame('Passenger Car', $type->name);
        $this->assertSame(449, $type->makeId);
    }

    public function test_vehicle_types_for_make_id_tolerates_missing_make_fields(): void
    {
        $response = $this->fakeVpic('vehicle_types_for_make_id')->vehicleTypesForMakeId(449);

        $type = $response->first();
        $this->assertGreaterThan(0, $type->id);
        $this->assertNull($type->makeId);
        $this->assertStringContainsString('GetVehicleTypesForMakeId/449', (string) $this->lastRequest()->getUri());
    }

    public function test_vehicle_variable_list_flags_lookups(): void
    {
        $response = $this->fakeVpic('vehicle_variable_list')->vehicleVariableList();

        $this->assertContainsOnlyInstancesOf(Variable::class, $response->all());

        $lookups = array_filter($response->all(), static fn (Variable $v): bool => $v->isLookup());
        $this->assertNotEmpty($lookups);
    }

    public function test_vehicle_variable_values(): void
    {
        $response = $this->fakeVpic('vehicle_variable_values')->vehicleVariableValues('battery type');

        $this->assertContainsOnlyInstancesOf(VariableValue::class, $response->all());
        $value = $response->first();
        $this->assertSame('Battery Type', $value->elementName);
        $this->assertSame('Lead Acid/Lead', $value->name);
    }

    public function test_equipment_plant_codes_sends_all_filters(): void
    {
        $response = $this->fakeVpic('equipment_plant_codes')
            ->equipmentPlantCodes(2015, 1, 'New');

        $this->assertContainsOnlyInstancesOf(PlantCode::class, $response->all());
        $this->assertNotSame('', $response->first()->dotCode);

        $query = $this->lastRequest()->getUri()->getQuery();
        $this->assertStringContainsString('year=2015', $query);
        $this->assertStringContainsString('equipmentType=1', $query);
        $this->assertStringContainsString('reportType=New', $query);
    }

    public function test_parts(): void
    {
        $response = $this->fakeVpic('parts')
            ->parts(565, '1/1/2015', '5/1/2015', page: 1);

        $this->assertContainsOnlyInstancesOf(Part::class, $response->all());
        $part = $response->first();
        $this->assertSame('ORG10655', $part->name);
        $this->assertSame('565', $part->type);
        $this->assertNull($part->modelYearFrom);

        $query = $this->lastRequest()->getUri()->getQuery();
        $this->assertStringContainsString('fromDate=1%2F1%2F2015', $query);
    }

    public function test_canadian_vehicle_specifications_flattens_specs(): void
    {
        $response = $this->fakeVpic('canadian_vehicle_specifications')
            ->canadianVehicleSpecifications(2011, 'Acura');

        $this->assertContainsOnlyInstancesOf(CanadianSpecification::class, $response->all());
        $csx = $response->first();
        $this->assertSame('ACURA', $csx->make());
        $this->assertSame('CSX', $csx->model());
        $this->assertSame(2011, $csx->modelYear());
        $this->assertSame('2700', $csx->get('Wheelbase'));

        $query = $this->lastRequest()->getUri()->getQuery();
        $this->assertStringContainsString('make=Acura', $query);
        $this->assertStringContainsString('units=Metric', $query);
    }
}
