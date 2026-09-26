<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Console;

enum AssertionPurpose: string
{
    case ConsoleEntry = 'console-entry';
    case Mcp = 'mcp';
}
