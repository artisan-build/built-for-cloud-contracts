<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts;

final class OwnershipClaim
{
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
