<?php

require __DIR__.'/vendor/autoload.php';

use Mattsplat\VinDecode\Vpic;

$vpic = new Vpic();

// Decode a VIN into a single flat object.
$vehicle = $vpic->decodeVinFlat('5UXWX7C5*BA', 2011);

printf(
    "%s %s %s (%s)\n",
    $vehicle->modelYear(),
    $vehicle->make(),
    $vehicle->model(),
    $vehicle->bodyClass(),
);

// List every model Honda has registered.
foreach ($vpic->modelsForMake('Honda') as $model) {
    echo $model->name, "\n";
}
