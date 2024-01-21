<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

interface FilterClearUrlGeneratorInterface
{
    public function generate(FilterView ...$filterViews): string;
}
