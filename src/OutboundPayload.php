<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts;

use InvalidArgumentException;

final readonly class OutboundPayload
{
    public string $product;

    public string $kind;

    public int $schemaVersion;

    public PayloadDisposition $disposition;

    public mixed $data;

    /** @var array<string, mixed> */
    public array $attributes;

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    public function __construct(
        string $product,
        string $kind,
        int $schemaVersion,
        PayloadDisposition $disposition,
        mixed $data,
        array $attributes,
    ) {
        if ($product === '') {
            throw new InvalidArgumentException('The product must not be empty.');
        }

        if ($kind === '') {
            throw new InvalidArgumentException('The kind must not be empty.');
        }

        if ($schemaVersion < 1) {
            throw new InvalidArgumentException('The schemaVersion must be at least 1.');
        }

        $validatedAttributes = [];

        foreach ($attributes as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('The attributes keys must be strings.');
            }

            $validatedAttributes[$key] = $value;
        }

        $this->product = $product;
        $this->kind = $kind;
        $this->schemaVersion = $schemaVersion;
        $this->disposition = $disposition;
        $this->data = $data;
        $this->attributes = $validatedAttributes;
    }
}
