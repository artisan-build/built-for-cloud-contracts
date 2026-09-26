<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts;

use ArtisanBuild\BuiltForCloudContracts\Console\ConsoleKeyring;

final class MetadataShape
{
    public const string TOKEN = '/^(?=.{1,64}$)[a-z0-9]+(?:[._:-][a-z0-9]+)*$/D';

    public const string SEMVER = '/^(?=.{1,32}$)\d{1,6}\.\d{1,6}\.\d{1,6}(?:-[0-9a-z]+(?:[.-][0-9a-z]+)*)?(?:\+[0-9a-z]+(?:[.-][0-9a-z]+)*)?$/D';

    public const string TIMESTAMP = '/^(?=.{1,40}$)\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:?\d{2})$/D';

    public const string CONSOLE_KEY_ID = ConsoleKeyring::KEY_ID_PATTERN;

    public static function isToken(string $value): bool
    {
        return preg_match(self::TOKEN, $value) === 1;
    }

    public static function isSemver(string $value): bool
    {
        return preg_match(self::SEMVER, $value) === 1;
    }

    public static function isTimestamp(string $value): bool
    {
        return preg_match(self::TIMESTAMP, $value) === 1;
    }

    public static function isConsoleKeyId(string $value): bool
    {
        return preg_match(self::CONSOLE_KEY_ID, $value) === 1;
    }
}
