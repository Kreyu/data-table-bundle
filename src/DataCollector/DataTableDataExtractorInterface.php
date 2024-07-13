<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;

interface DataTableDataExtractorInterface
{
    public function extractFilter(DataTableInterface $dataTable, string $field, FilterData $data): array;
}
