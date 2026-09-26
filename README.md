# Built for Cloud Contracts

Pure PHP protocol contracts shared by [Built for Cloud](https://github.com/artisan-build/built-for-cloud)
and Scalpels.

This package is intentionally limited to constants, closed value vocabularies, and pure validators.
It does not provide routes, migrations, service providers, models, or runtime services.

## Public API

- `ArtisanBuild\BuiltForCloudContracts\BuiltForCloud` exposes the shared protocol `API_VERSION`.
- `ArtisanBuild\BuiltForCloudContracts\Mcp\Classification` defines the metadata and content classifications.
- `ArtisanBuild\BuiltForCloudContracts\Console\ConsoleKeyring` defines and validates console key identifiers.
- `ArtisanBuild\BuiltForCloudContracts\Console\ConsoleRole` defines the console role vocabulary.
- `ArtisanBuild\BuiltForCloudContracts\Console\AssertionVerifier` exposes assertion format and size limits.
- `ArtisanBuild\BuiltForCloudContracts\Console\AssertionPurpose` defines assertion purposes.
- `ArtisanBuild\BuiltForCloudContracts\Console\AssertionBurn` defines burn retention and pure mint identity hashing.
- `ArtisanBuild\BuiltForCloudContracts\Vitals\VitalsPayload` exposes vitals payload version and numeric bounds.
- `ArtisanBuild\BuiltForCloudContracts\MetadataShape` defines and validates shared metadata string shapes.
- `ArtisanBuild\BuiltForCloudContracts\OwnershipClaim` provides pure ownership-token hashing.

`BuiltForCloud::API_VERSION` is the shared protocol version. It is separate from the Built for Cloud
package's release version, which remains owned by that package and is not exposed here.

The package contains no persistence, cryptography, token parsing or verification, payload assembly,
framework integration, or other runtime services. Consumers own those behaviors and depend on this
package only for the protocol values and pure operations listed above.

## Requirements

- PHP 8.4 or later

## Installation

```bash
composer require artisan-build/built-for-cloud-contracts
```

## Development

```bash
composer install
composer test
composer stan
composer lint:test
```

## License

Built for Cloud Contracts is open-sourced software licensed under the [MIT license](LICENSE).
