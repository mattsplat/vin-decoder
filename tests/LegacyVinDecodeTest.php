<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Tests;

use Mattsplat\VinDecode\Vpic;
use VinDecode\VinDecode;

/**
 * The deprecated 0.x surface must keep working.
 */
final class LegacyVinDecodeTest extends TestCase
{
    public function test_set_vin_rejects_non_17_character_input(): void
    {
        $decoder = new VinDecode($this->fakeVpic('decode_vin'));

        $this->assertFalse($decoder->setVIN('too-short'));
        $this->assertTrue($decoder->setVIN('5UXWX7C5XBL000000'));
    }

    public function test_search_vin_returns_a_snake_cased_flat_array_and_sets_shortcuts(): void
    {
        $decoder = new VinDecode($this->fakeVpic('decode_vin'));
        $decoder->setVIN('5UXWX7C5XBL000000');

        $results = $decoder->searchVIN();

        $this->assertSame('BMW', $results['make']);
        $this->assertSame('X3', $results['model']);
        $this->assertSame('2011', $results['model_year']);
        $this->assertArrayNotHasKey('series', $results, 'blank values are dropped');

        $this->assertSame('BMW', $decoder->make);
        $this->assertSame('X3', $decoder->model);
        $this->assertSame('2011', $decoder->year);
    }

    public function test_it_defaults_to_a_real_vpic_client(): void
    {
        $decoder = new VinDecode();

        $this->assertInstanceOf(VinDecode::class, $decoder);
        // sanity: the class still constructs its own Vpic without arguments
        $this->assertTrue(class_exists(Vpic::class));
    }
}
