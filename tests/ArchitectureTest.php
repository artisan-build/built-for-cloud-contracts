<?php

declare(strict_types=1);

use ArtisanBuild\BuiltForCloudContracts\BuiltForCloud;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionBurn;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionPurpose;
use ArtisanBuild\BuiltForCloudContracts\Console\AssertionVerifier;
use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleKeyring;
use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleRole;
use ArtisanBuild\BuiltForCloudContracts\Mail\ManagedMail;
use ArtisanBuild\BuiltForCloudContracts\Mcp\Classification;
use ArtisanBuild\BuiltForCloudContracts\Mcp\Effect;
use ArtisanBuild\BuiltForCloudContracts\MetadataShape;
use ArtisanBuild\BuiltForCloudContracts\OutboundPayload;
use ArtisanBuild\BuiltForCloudContracts\OwnershipClaim;
use ArtisanBuild\BuiltForCloudContracts\PayloadDisposition;
use ArtisanBuild\BuiltForCloudContracts\PayloadFilter;
use ArtisanBuild\BuiltForCloudContracts\Vitals\VitalsPayload;
use Tests\Support\SourceDeclarations;

require_once __DIR__.'/Support/SourceDeclarations.php';

it('contains exactly the fifteen public contract source declarations', function (): void {
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
        'Mail/ManagedMail.php',
        'Mcp/Classification.php',
        'Mcp/Effect.php',
        'MetadataShape.php',
        'OutboundPayload.php',
        'OwnershipClaim.php',
        'PayloadDisposition.php',
        'PayloadFilter.php',
        'Vitals/VitalsPayload.php',
    ]);

    $expectedDeclarations = [
        BuiltForCloud::class,
        AssertionBurn::class,
        AssertionPurpose::class,
        AssertionVerifier::class,
        ConsoleKeyring::class,
        ConsoleRole::class,
        ManagedMail::class,
        Classification::class,
        Effect::class,
        MetadataShape::class,
        OutboundPayload::class,
        OwnershipClaim::class,
        PayloadDisposition::class,
        PayloadFilter::class,
        VitalsPayload::class,
    ];

    $declarations = [];

    foreach ($files as $file) {
        $declarations = [
            ...$declarations,
            ...SourceDeclarations::fromCode((string) file_get_contents($sourceRoot.'/'.$file)),
        ];
    }

    sort($declarations);

    expect($declarations)->toBe($expectedDeclarations);
});

it('detects extra class-like declarations inside a known source file shape', function (): void {
    $source = <<<'PHP'
<?php

namespace ArtisanBuild\BuiltForCloudContracts;

final class BuiltForCloud {}
interface ExtraContract {}
trait ExtraBehavior {}
enum ExtraVocabulary { case Added; }
PHP;

    expect(SourceDeclarations::fromCode($source))->toBe([
        'ArtisanBuild\\BuiltForCloudContracts\\BuiltForCloud',
        'ArtisanBuild\\BuiltForCloudContracts\\ExtraBehavior',
        'ArtisanBuild\\BuiltForCloudContracts\\ExtraContract',
        'ArtisanBuild\\BuiltForCloudContracts\\ExtraVocabulary',
    ]);
});

it('pins every user-defined public method, constant, and enum case', function (): void {
    $expected = [
        BuiltForCloud::class => [
            'methods' => [],
            'constants' => ['API_VERSION'],
            'cases' => [],
        ],
        AssertionBurn::class => [
            'methods' => ['mintHash'],
            'constants' => ['PRUNE_MARGIN_SECONDS'],
            'cases' => [],
        ],
        AssertionPurpose::class => [
            'methods' => [],
            'constants' => [],
            'cases' => ['ConsoleEntry', 'Mcp'],
        ],
        AssertionVerifier::class => [
            'methods' => [],
            'constants' => [
                'HEADER',
                'MAX_DISPLAY_LENGTH',
                'MAX_IDENTITY_LENGTH',
                'MAX_ID_LENGTH',
                'MAX_TOKEN_LENGTH',
                'STATE_DIGEST_PATTERN',
            ],
            'cases' => [],
        ],
        ConsoleKeyring::class => [
            'methods' => ['isValidKeyId'],
            'constants' => ['KEY_ID_PATTERN', 'PUBLIC_KEY_BYTES'],
            'cases' => [],
        ],
        ConsoleRole::class => [
            'methods' => ['values'],
            'constants' => [],
            'cases' => ['Admin', 'Member'],
        ],
        ManagedMail::class => [
            'methods' => ['isMessageSizeWithinLimit', 'isRecipientCountWithinLimit'],
            'constants' => [
                'ATTACHMENT_FIELDS',
                'CONTRACT_VERSION',
                'MAX_MESSAGE_BYTES',
                'MAX_RECIPIENTS',
                'PATH',
                'PAYLOAD_FIELDS',
                'RECIPIENT_FIELDS',
            ],
            'cases' => [],
        ],
        Classification::class => [
            'methods' => [],
            'constants' => [],
            'cases' => ['Content', 'Metadata'],
        ],
        Effect::class => [
            'methods' => [],
            'constants' => [],
            'cases' => ['Destructive', 'Read', 'Write'],
        ],
        MetadataShape::class => [
            'methods' => ['isConsoleKeyId', 'isSemver', 'isTimestamp', 'isToken'],
            'constants' => ['CONSOLE_KEY_ID', 'SEMVER', 'TIMESTAMP', 'TOKEN'],
            'cases' => [],
        ],
        OutboundPayload::class => [
            'methods' => ['__construct'],
            'constants' => [],
            'cases' => [],
        ],
        OwnershipClaim::class => [
            'methods' => ['hashToken'],
            'constants' => [],
            'cases' => [],
        ],
        PayloadDisposition::class => [
            'methods' => [],
            'constants' => [],
            'cases' => ['Deliverable', 'Droppable'],
        ],
        PayloadFilter::class => [
            'methods' => ['filter'],
            'constants' => [],
            'cases' => [],
        ],
        VitalsPayload::class => [
            'methods' => [],
            'constants' => ['MAX_AGE_SECONDS', 'MAX_HEADLINE_MAGNITUDE', 'VERSION'],
            'cases' => [],
        ],
    ];
    $actual = [];

    foreach (array_keys($expected) as $class) {
        $reflection = new ReflectionClass($class);
        $methods = [];
        $constants = [];
        $cases = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isUserDefined() && $method->getDeclaringClass()->getName() === $class) {
                $methods[] = $method->getName();
            }
        }

        foreach ($reflection->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC) as $constant) {
            if ($constant->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            if ($constant->isEnumCase()) {
                $cases[] = $constant->getName();
            } else {
                $constants[] = $constant->getName();
            }
        }

        sort($methods);
        sort($constants);
        sort($cases);

        $actual[$class] = compact('methods', 'constants', 'cases');
    }

    expect($actual)->toBe($expected);
});

it('pins the outbound payload public properties', function (): void {
    $properties = array_map(
        static fn (ReflectionProperty $property): string => $property->getName(),
        (new ReflectionClass(OutboundPayload::class))->getProperties(ReflectionProperty::IS_PUBLIC),
    );

    expect($properties)->toBe(['product', 'kind', 'schemaVersion', 'disposition', 'data', 'attributes']);
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
