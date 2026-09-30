<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts;

enum PayloadDisposition: string
{
    case Droppable = 'droppable';
    case Deliverable = 'deliverable';
}
