<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Console;

final class AssertionBurn
{
    public const int PRUNE_MARGIN_SECONDS = 60;

    public static function mintHash(string $issuer, string $mintId): string
    {
        return hash('sha256', strlen($issuer).':'.$issuer.':'.strlen($mintId).':'.$mintId);
    }
}
