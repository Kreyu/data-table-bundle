<?php
declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;

class DataTableDataExtractor implements DataTableDataExtractorInterface
{
    public function extractFilter(DataTableInterface $dataTable, string $field, FilterData $data): array
    {
        return [
            'name' => $field,
            'operator' => $data->getOperator()->getLabel(),
            'value' => $data->getValue(),
            'type' => $dataTable->getFilter($field)->getConfig()->getType()->getInnerType()::class,
        ];
    }
}