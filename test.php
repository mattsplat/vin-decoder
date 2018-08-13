<?php
require 'VinDecode.php';

$vin_decode = new VinDecode\VinDecode();

$vin_decode->setVIN('4G2JB3249VB205377');
$vin_decode->searchVIN();


var_dump($vin_decode);