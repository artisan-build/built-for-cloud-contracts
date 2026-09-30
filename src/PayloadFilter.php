<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts;

interface PayloadFilter
{
    public function filter(OutboundPayload $payload): ?OutboundPayload;
}
