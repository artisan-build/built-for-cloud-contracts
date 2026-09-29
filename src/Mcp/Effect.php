<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Mcp;

enum Effect: string
{
    case Read = 'read';
    case Write = 'write';
    case Destructive = 'destructive';
}
