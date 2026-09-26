<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Console;

final class AssertionVerifier
{
    public const string HEADER = 'v4.public.';

    public const int MAX_DISPLAY_LENGTH = 120;

    public const int MAX_IDENTITY_LENGTH = 255;

    public const int MAX_ID_LENGTH = 64;

    public const string STATE_DIGEST_PATTERN = '/^[0-9a-f]{64}\z/';

    public const int MAX_TOKEN_LENGTH = 4096;
}
