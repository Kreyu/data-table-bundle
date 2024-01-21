<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class DateActiveFilterFormatter
{
    public function __invoke(FilterData $data, FilterInterface $filter): string
    {
        $value = $data->getValue();

        if ($value instanceof \DateTimeInterface) {
            return $value->format($filter->getConfig()->getOption('form_options')['input_format'] ?? 'Y-m-d');
        }

        return (string) $value;
    }
}
