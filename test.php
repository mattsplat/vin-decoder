<?php
$url = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/1HGFA16538L065?format=json';
$response = http_get($url, array("timeout"=>1), $info);

var_dump($info);