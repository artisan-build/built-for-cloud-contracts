<?php

declare(strict_types=1);

use ArtisanBuild\BuiltForCloudContracts\BuiltForCloud;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionBurn;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionPurpose;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionVerifier;
use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleKeyring;
use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleRole;
use ArtisanBuild\BuiltForCloudContracts\Mcp\Classification;
use ArtisanBuild\BuiltForCloudContracts\MetadataShape;
use ArtisanBuild\BuiltForCloudContracts\OwnershipClaim;
use ArtisanBuild\BuiltForCloudContracts\Vitals\VitalsPayload;

it('contains exactly the ten public contract source classes', function (): void {
    $sourceRoot = dirname(__DIR__).'/src';
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceRoot, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if (! $file instanceof SplFileInfo) {
            continue;
        }

        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = str_replace($sourceRoot.'/', '', $file->getPathname());
        }
    }

    sort($files);

    expect($files)->toBe([
        'BuiltForCloud.php',
        'Console/AssertionBurn.php',
        'Console/AssertionPurpose.php',
        'Console/AssertionVerifier.php',
        'Console/ConsoleKeyring.php',
        'Console/ConsoleRole.php',
        'Mcp/Classification.php',
        'MetadataShape.php',
        'OwnershipClaim.php',
        'Vitals/VitalsPayload.php',
    ]);

    $classes = [
        BuiltForCloud::class,
        AssertionBurn::class,
        AssertionPurpose::class,
        AssertionVerifier::class,
        ConsoleKeyring::class,
        ConsoleRole::class,
        Classification::class,
        MetadataShape::class,
        OwnershipClaim::class,
        VitalsPayload::class,
    ];

    expect($classes)->toHaveCount(10);

    foreach ($classes as $class) {
        expect(class_exists($class) || enum_exists($class))->toBeTrue($class)
            ->and((new ReflectionClass($class))->getNamespaceName())
            ->toStartWith('ArtisanBuild\\BuiltForCloudContracts');
    }
});

it('has a PHP-only runtime and no framework or runtime-service source dependencies', function (): void {
    /** @var array{require: array<string, string>} $composer */
    $composer = json_decode(
        (string) file_get_contents(dirname(__DIR__).'/composer.json'),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($composer['require'])->toBe(['php' => '^8.4']);

    $source = '';
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(dirname(__DIR__).'/src', FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if (! $file instanceof SplFileInfo) {
            continue;
        }

        if ($file->isFile() && $file->getExtension() === 'php') {
            $source .= (string) file_get_contents($file->getPathname());
        }
    }

    foreach (['Illuminate\\', 'Laravel\\', 'Carbon\\', 'ParagonIE\\'] as $forbiddenNamespace) {
        expect($source)->not->toContain($forbiddenNamespace);
    }

    expect($source)->not->toMatch('/namespace\s+ArtisanBuild\\\\BuiltForCloud(?:\\\\|;)/');
    expect($source)->not->toMatch('/\b(?:extends\s+Model|ServiceProvider|Route::|Migration)\b/');
});
