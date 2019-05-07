<?php
require 'src/VinDecode.php';
use VinDecode\VinDecode;

$vin_decode = new VinDecode();

$vin_decode->setVIN('4G2JB3249VB205377');
$vin_decode->searchVIN();


var_dump($vin_decode);