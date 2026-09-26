<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Console;

final class ConsoleKeyring
{
    public const int PUBLIC_KEY_BYTES = 32;

    public const string KEY_ID_PATTERN = '/^[A-Za-z0-9._-]{1,64}\z/';

    public static function isValidKeyId(string $keyId): bool
    {
        return preg_match(self::KEY_ID_PATTERN, $keyId) === 1;
    }
}
