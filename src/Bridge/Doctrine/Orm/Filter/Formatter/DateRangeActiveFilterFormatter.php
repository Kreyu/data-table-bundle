<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Symfony\Component\Translation\TranslatableMessage;

class DateRangeActiveFilterFormatter
{
    public function __invoke(FilterData $data)
    {
        $value = $data->getValue();

        $dateFrom = $value['from'];
        $dateTo = $value['to'];

        if (null !== $dateFrom && null === $dateTo) {
            return new TranslatableMessage('After %date%', ['%date%' => $dateFrom->format('Y-m-d')], 'KreyuDataTable');
        }

        if (null === $dateFrom && null !== $dateTo) {
            return new TranslatableMessage('Before %date%', ['%date%' => $dateTo->format('Y-m-d')], 'KreyuDataTable');
        }

        if ($dateFrom == $dateTo) {
            return $dateFrom->format('Y-m-d');
        }

        return $dateFrom->format('Y-m-d').' - '.$dateTo->format('Y-m-d');
    }
}
