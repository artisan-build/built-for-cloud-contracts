<?php

declare(strict_types=1);

use ArtisanBuild\BuiltForCloudContracts\OutboundPayload;
use ArtisanBuild\BuiltForCloudContracts\PayloadDisposition;
use ArtisanBuild\BuiltForCloudContracts\PayloadFilter;

it('pins the complete payload disposition vocabulary in order', function (): void {
    $cases = array_map(
        static fn (PayloadDisposition $case): array => [$case->name, $case->value],
        PayloadDisposition::cases(),
    );

    expect($cases)->toBe([
        ['Droppable', 'droppable'],
        ['Deliverable', 'deliverable'],
    ]);
});

it('preserves an outbound payload without normalizing product data or attributes', function (): void {
    $data = [
        'messages' => [
            ['role' => 'user', 'content' => ['text' => 'Hello']],
        ],
        'usage' => ['input_tokens' => 12, 'output_tokens' => 7],
    ];
    $attributes = [
        'subject' => 'account:123',
        'capture_mode' => 'full',
    ];
    $payload = new OutboundPayload(
        product: 'assay',
        kind: 'run.step',
        schemaVersion: 2,
        disposition: PayloadDisposition::Droppable,
        data: $data,
        attributes: $attributes,
    );
    $reflection = new ReflectionClass($payload);

    expect($reflection->isFinal())->toBeTrue()
        ->and($reflection->isReadOnly())->toBeTrue()
        ->and(array_map(
            static fn (ReflectionProperty $property): string => $property->getName(),
            $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
        ))->toBe(['product', 'kind', 'schemaVersion', 'disposition', 'data', 'attributes'])
        ->and($payload->product)->toBe('assay')
        ->and($payload->kind)->toBe('run.step')
        ->and($payload->schemaVersion)->toBe(2)
        ->and($payload->disposition)->toBe(PayloadDisposition::Droppable)
        ->and($payload->data)->toBe($data)
        ->and($payload->attributes)->toBe($attributes);
});

it('rejects invalid universal outbound payload invariants', function (Closure $makePayload, string $field): void {
    try {
        $makePayload();
    } catch (InvalidArgumentException $exception) {
        expect($exception->getMessage())->toContain($field);

        return;
    }

    throw new AssertionError("Expected invalid {$field} to be rejected.");
})->with([
    'empty product' => [
        fn (): OutboundPayload => new OutboundPayload('', 'run.step', 1, PayloadDisposition::Droppable, [], []),
        'product',
    ],
    'empty kind' => [
        fn (): OutboundPayload => new OutboundPayload('assay', '', 1, PayloadDisposition::Droppable, [], []),
        'kind',
    ],
    'schema version below one' => [
        fn (): OutboundPayload => new OutboundPayload('assay', 'run.step', 0, PayloadDisposition::Droppable, [], []),
        'schemaVersion',
    ],
    'non-string attribute key' => [
        fn (): OutboundPayload => new OutboundPayload('assay', 'run.step', 1, PayloadDisposition::Droppable, [], [0 => 'subject']),
        'attributes',
    ],
]);

it('defines the nullable payload filter contract', function (): void {
    $reflection = new ReflectionClass(PayloadFilter::class);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    expect(array_map(
        static fn (ReflectionMethod $method): string => $method->getName(),
        $methods,
    ))->toBe(['filter']);

    $parameterType = $methods[0]->getParameters()[0]->getType();
    $returnType = $methods[0]->getReturnType();

    if (! $parameterType instanceof ReflectionNamedType || ! $returnType instanceof ReflectionNamedType) {
        throw new AssertionError('PayloadFilter types must both be named types.');
    }

    expect($parameterType->getName())->toBe(OutboundPayload::class)
        ->and($parameterType->allowsNull())->toBeFalse()
        ->and($returnType->getName())->toBe(OutboundPayload::class)
        ->and($returnType->allowsNull())->toBeTrue();

    $payload = new OutboundPayload('assay', 'run.step', 1, PayloadDisposition::Droppable, [], []);
    $filter = new class implements PayloadFilter
    {
        public function filter(OutboundPayload $payload): ?OutboundPayload
        {
            return $payload->product === 'drop' ? null : $payload;
        }
    };
    $droppablePayload = new OutboundPayload('drop', 'run.step', 1, PayloadDisposition::Droppable, [], []);

    expect($filter->filter($payload))->toBe($payload)
        ->and($filter->filter($droppablePayload))->toBeNull();
});
