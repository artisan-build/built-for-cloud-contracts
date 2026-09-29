<?php

declare(strict_types=1);

use ArtisanBuild\BuiltForCloudContracts\BuiltForCloud;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionBurn;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionPurpose;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionVerifier;
use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleKeyring;
use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleRole;
use ArtisanBuild\BuiltForCloudContracts\Mcp\Classification;
use ArtisanBuild\BuiltForCloudContracts\Mcp\Effect;
use ArtisanBuild\BuiltForCloudContracts\MetadataShape;
use ArtisanBuild\BuiltForCloudContracts\OwnershipClaim;
use ArtisanBuild\BuiltForCloudContracts\Vitals\VitalsPayload;

it('exposes only the shared protocol version', function (): void {
    expect(BuiltForCloud::API_VERSION)->toBe(2)
        ->and(array_keys((new ReflectionClass(BuiltForCloud::class))->getConstants()))->toBe(['API_VERSION'])
        ->and(defined(BuiltForCloud::class.'::VERSION'))->toBeFalse();
});

it('pins the complete classification vocabulary in order', function (): void {
    $cases = array_map(
        static fn (Classification $case): array => [$case->name, $case->value],
        Classification::cases(),
    );

    expect($cases)->toBe([
        ['Metadata', 'metadata'],
        ['Content', 'content'],
    ]);
});

it('pins the complete effect vocabulary in order', function (): void {
    $cases = array_map(
        static fn (Effect $case): array => [$case->name, $case->value],
        Effect::cases(),
    );

    expect($cases)->toBe([
        ['Read', 'read'],
        ['Write', 'write'],
        ['Destructive', 'destructive'],
    ]);
});

it('pins the complete console role vocabulary and values in order', function (): void {
    $cases = array_map(
        static fn (ConsoleRole $case): array => [$case->name, $case->value],
        ConsoleRole::cases(),
    );

    expect($cases)->toBe([
        ['Admin', 'admin'],
        ['Member', 'member'],
    ])->and(ConsoleRole::values())->toBe(['admin', 'member']);
});

it('pins the complete assertion purpose vocabulary in order', function (): void {
    $cases = array_map(
        static fn (AssertionPurpose $case): array => [$case->name, $case->value],
        AssertionPurpose::cases(),
    );

    expect($cases)->toBe([
        ['ConsoleEntry', 'console-entry'],
        ['Mcp', 'mcp'],
    ]);
});

it('pins and validates the console key id contract', function (): void {
    expect(ConsoleKeyring::PUBLIC_KEY_BYTES)->toBe(32)
        ->and(ConsoleKeyring::KEY_ID_PATTERN)->toBe('/^[A-Za-z0-9._-]{1,64}\z/');

    foreach (['a', 'K2.probe_key-id', str_repeat('a', 64)] as $valid) {
        expect(ConsoleKeyring::isValidKeyId($valid))->toBeTrue($valid);
    }

    foreach (['', str_repeat('a', 65), 'has space', 'has/slash', "control\x00character", "valid-looking\n"] as $invalid) {
        expect(ConsoleKeyring::isValidKeyId($invalid))->toBeFalse(bin2hex($invalid));
    }
});

it('pins the assertion contract limits and state digest shape', function (): void {
    expect(AssertionVerifier::HEADER)->toBe('v4.public.')
        ->and(AssertionVerifier::MAX_DISPLAY_LENGTH)->toBe(120)
        ->and(AssertionVerifier::MAX_IDENTITY_LENGTH)->toBe(255)
        ->and(AssertionVerifier::MAX_ID_LENGTH)->toBe(64)
        ->and(AssertionVerifier::STATE_DIGEST_PATTERN)->toBe('/^[0-9a-f]{64}\z/')
        ->and(AssertionVerifier::MAX_TOKEN_LENGTH)->toBe(4096);

    expect(preg_match(AssertionVerifier::STATE_DIGEST_PATTERN, str_repeat('a', 64)))->toBe(1);

    foreach ([str_repeat('A', 64), str_repeat('a', 63), str_repeat('a', 65), str_repeat('a', 64)."\n"] as $invalid) {
        expect(preg_match(AssertionVerifier::STATE_DIGEST_PATTERN, $invalid))->toBe(0, bin2hex($invalid));
    }
});

it('uses the exact length-delimited burn identity', function (): void {
    $issuer = 'https://issuer.example';
    $mintId = 'mint-123';
    $encoded = strlen($issuer).':'.$issuer.':'.strlen($mintId).':'.$mintId;

    expect(AssertionBurn::PRUNE_MARGIN_SECONDS)->toBe(60)
        ->and(AssertionBurn::mintHash($issuer, $mintId))->toBe(hash('sha256', $encoded))
        ->and(AssertionBurn::mintHash('iss', 'mid'))->toBe('33ea3e6f245152a0fac47257c8b21408c4fe744b07b5eed5043c40af1c7da682')
        ->and(AssertionBurn::mintHash('ab', 'c'))->not->toBe(AssertionBurn::mintHash('a', 'bc'));
});

it('pins the vitals payload bounds', function (): void {
    expect(VitalsPayload::VERSION)->toBe(1)
        ->and(VitalsPayload::MAX_HEADLINE_MAGNITUDE)->toBe(1.0e15)
        ->and(VitalsPayload::MAX_AGE_SECONDS)->toBe(3153600000);
});

it('pins the shared metadata shape patterns', function (): void {
    expect(MetadataShape::TOKEN)->toBe('/^(?=.{1,64}$)[a-z0-9]+(?:[._:-][a-z0-9]+)*$/D')
        ->and(MetadataShape::SEMVER)->toBe('/^(?=.{1,32}$)\d{1,6}\.\d{1,6}\.\d{1,6}(?:-[0-9a-z]+(?:[.-][0-9a-z]+)*)?(?:\+[0-9a-z]+(?:[.-][0-9a-z]+)*)?$/D')
        ->and(MetadataShape::TIMESTAMP)->toBe('/^(?=.{1,40}$)\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:?\d{2})$/D')
        ->and(MetadataShape::CONSOLE_KEY_ID)->toBe('/^[A-Za-z0-9._-]{1,64}\z/')
        ->and(MetadataShape::CONSOLE_KEY_ID)->toBe(ConsoleKeyring::KEY_ID_PATTERN);
});

it('validates token metadata boundaries through the public method', function (): void {
    foreach (['a', 'metadata:read', 'one.two_three-four', str_repeat('a', 64)] as $valid) {
        expect(MetadataShape::isToken($valid))->toBeTrue($valid);
    }

    foreach (['', str_repeat('a', 65), 'Uppercase', 'free text', '-leading', 'double--separator', 'has/slash', "valid\n"] as $invalid) {
        expect(MetadataShape::isToken($invalid))->toBeFalse(bin2hex($invalid));
    }
});

it('validates semver metadata boundaries through the public method', function (): void {
    foreach (['0.0.0', '1.2.3-alpha.1+build.9', '123456.123456.123456', '1.2.3+'.str_repeat('a', 26)] as $valid) {
        expect(MetadataShape::isSemver($valid))->toBeTrue($valid);
    }

    foreach (['', '1.2', '1.2.3-RC1', '1.2.3+Jane.Operator', '1.2.3-alpha_1', '1..2.3', '1234567.2.3', '1.2.3+'.str_repeat('a', 27), "1.2.3\n"] as $invalid) {
        expect(MetadataShape::isSemver($invalid))->toBeFalse(bin2hex($invalid));
    }
});

it('validates timestamp metadata boundaries through the public method', function (): void {
    foreach (['2026-09-26T12:34:56Z', '2026-09-26 12:34:56+0000', '2026-09-26T12:34:56.123456+00:00'] as $valid) {
        expect(MetadataShape::isTimestamp($valid))->toBeTrue($valid);
    }

    foreach (['', 'SATURDAY', '2026/09/26T12:34:56Z', '2026-09-26T12-34-56Z', '2026-09-26T12:34:56', '2026-09-26T12:34:56.'.str_repeat('1', 20).'Z', "2026-09-26T12:34:56Z\n"] as $invalid) {
        expect(MetadataShape::isTimestamp($invalid))->toBeFalse(bin2hex($invalid));
    }
});

it('validates console key id metadata boundaries through the public method', function (): void {
    foreach (['a', 'K2.probe_key-id', str_repeat('a', 64)] as $valid) {
        expect(MetadataShape::isConsoleKeyId($valid))->toBeTrue($valid);
    }

    foreach (['', str_repeat('a', 65), 'has space', 'has/slash', "control\x1Fcharacter", "valid-looking\n"] as $invalid) {
        expect(MetadataShape::isConsoleKeyId($invalid))->toBeFalse(bin2hex($invalid));
    }
});

it('hashes ownership claim tokens as lowercase sha256 over their exact bytes', function (): void {
    expect(OwnershipClaim::hashToken(''))->toBe('e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855')
        ->and(OwnershipClaim::hashToken('abc'))->toBe('ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad')
        ->and(OwnershipClaim::hashToken('abc'))->toBe(OwnershipClaim::hashToken('abc'))
        ->and(OwnershipClaim::hashToken('abc'))->not->toBe(OwnershipClaim::hashToken("abc\x00"))
        ->and(OwnershipClaim::hashToken('abc'))->toMatch('/^[0-9a-f]{64}\z/');
});
