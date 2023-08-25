<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

enum ExportStrategy: string
{
    case IncludeCurrentPage = 'include-current-page';
    case IncludeAll = 'include-all';

    public function getLabel(): string
    {
        return match ($this) {
            self::IncludeCurrentPage => 'Include current page',
            self::IncludeAll => 'Include all',
        };
    }
}
