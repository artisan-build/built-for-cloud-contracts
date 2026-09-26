<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

it('registers the public package namespace to the source directory', function (): void {
    $loaders = array_filter(
        spl_autoload_functions(),
        static fn (mixed $loader): bool => is_array($loader) && $loader[0] instanceof ClassLoader,
    );

    expect($loaders)->not->toBeEmpty();

    /** @var array{0: ClassLoader, 1: string} $loader */
    $loader = reset($loaders);
    $paths = $loader[0]->getPrefixesPsr4()['ArtisanBuild\\BuiltForCloudContracts\\'] ?? [];

    expect(array_map(realpath(...), $paths))->toBe([realpath(dirname(__DIR__).'/src')]);
});
