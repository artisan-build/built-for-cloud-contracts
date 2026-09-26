<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Console;

enum ConsoleRole: string
{
    case Admin = 'admin';
    case Member = 'member';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases(),
        );
    }
}
