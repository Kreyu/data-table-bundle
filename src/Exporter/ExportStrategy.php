<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

enum ExportStrategy: string
{
    case IncludeCurrentPage = 'include-current-page';
    case IncludeAll = 'include-all';

    /*
     * @deprecated use {@see ExportStrategy::IncludeCurrentPage} instead
     */
    case INCLUDE_CURRENT_PAGE = 'deprecated-include-current-page';

    /*
     * @deprecated use {@see ExportStrategy::IncludeAll} instead
     */
    case INCLUDE_ALL = 'deprecated-include-all';

    public function getLabel(): string
    {
        // TODO: Remove deprecated cases labels
        return match ($this) {
            self::INCLUDE_CURRENT_PAGE, self::IncludeCurrentPage => 'Include current page',
            self::INCLUDE_ALL, self::IncludeAll => 'Include all',
        };
    }

    /**
     * TODO: Remove this method after removing deprecated cases.
     */
    public function getNonDeprecatedCase(): self
    {
        return match ($this) {
            self::INCLUDE_CURRENT_PAGE => self::IncludeCurrentPage,
            self::INCLUDE_ALL => self::IncludeAll,
            default => $this,
        };
    }
}
