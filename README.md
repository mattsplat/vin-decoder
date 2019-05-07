# vin-decoder
### Install
`composer require mattsplat/vin-decode`

Uses National Highway Traffic Safety Administration's API to determine vehicle information

#### Use

```
$vin_decode = new VinDecode();

$vin_decode->setVIN('4G2JB3249VB205377');
$vin_decode->searchVIN();
```
