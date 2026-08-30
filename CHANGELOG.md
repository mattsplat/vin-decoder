# Changelog

All notable changes to `mattsplat/vin-decode` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

Full rewrite: complete coverage of the NHTSA vPIC vehicle API.

### Added
- `Mattsplat\VinDecode\Vpic` — one method per documented vPIC endpoint:
  - VIN decoding: `decodeVin`, `decodeVinFlat`, `decodeVinExtended`,
    `decodeVinFlatExtended`, `decodeVinBatch`
  - WMI: `decodeWmi`, `wmisForManufacturer`
  - Manufacturers & makes: `allManufacturers`, `manufacturerDetails`,
    `makesForManufacturer`, `makesForManufacturerAndYear`, `allMakes`
  - Models: `modelsForMake`, `modelsForMakeId`, `modelsForMakeYear`,
    `modelsForMakeIdYear`
  - Vehicle types: `makesForVehicleType`, `vehicleTypesForMake`,
    `vehicleTypesForMakeId`
  - Variables, equipment & parts: `vehicleVariableList`, `vehicleVariableValues`,
    `equipmentPlantCodes`, `parts`
  - Canadian specifications: `canadianVehicleSpecifications`
- `Mattsplat\VinDecode\Client` — Guzzle-backed HTTP wrapper; accepts a custom
  `ClientInterface` and base URI.
- Immutable typed DTOs for every response type under `Mattsplat\VinDecode\DTO`.
- `Mattsplat\VinDecode\Responses\Response` — iterable / countable / array-accessible
  wrapper over the `{Count, Message, SearchCriteria, Results}` envelope.
- `Mattsplat\VinDecode\Exceptions\VpicException` and `VpicRequestException`.
- Test suite (PHPUnit, mocked HTTP with fixtures captured from the live API),
  PHPStan (level 8) and Laravel Pint configs, and a GitHub Actions CI workflow
  (PHP 8.1–8.4).
- Package hygiene: `LICENSE` (MIT), this changelog, `CONTRIBUTING.md`,
  `SECURITY.md`, `.editorconfig`, `.gitattributes`, `docs/` guides.

### Changed
- **BREAKING:** root namespace is now `Mattsplat\VinDecode\`.
- **BREAKING:** minimum PHP version is 8.1.
- `composer.json` filled out with license, authors, keywords, dev dependencies
  and Composer scripts; adds `guzzlehttp/guzzle` as a runtime dependency.

### Deprecated
- `VinDecode\VinDecode` — kept as a thin backwards-compatible shim over `Vpic`
  (`setVIN()` / `searchVIN()` behave as before). Use `Mattsplat\VinDecode\Vpic`
  directly. Scheduled for removal in the next major release.

## [0.1.0] - 2018-08-13

### Added
- Initial `VinDecode` class: `setVIN()` + `searchVIN()` against
  `vehicles/DecodeVin`, returning a snake_cased flat array.

[Unreleased]: https://github.com/mattsplat/vin-decoder/compare/master...HEAD
