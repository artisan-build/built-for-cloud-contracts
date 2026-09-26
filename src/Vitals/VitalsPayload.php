<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Vitals;

final class VitalsPayload
{
    public const int VERSION = 1;

    public const float MAX_HEADLINE_MAGNITUDE = 1.0e15;

    public const int MAX_AGE_SECONDS = 3153600000;
}
