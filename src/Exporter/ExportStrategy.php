<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

enum ExportStrategy: string
{
    case INCLUDE_CURRENT_PAGE = 'include-current-page';
    case INCLUDE_ALL = 'include-all';
}
