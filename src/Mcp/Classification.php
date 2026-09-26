<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Mcp;

enum Classification: string
{
    case Metadata = 'metadata';
    case Content = 'content';
}
