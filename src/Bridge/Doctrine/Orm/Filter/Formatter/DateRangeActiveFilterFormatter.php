<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Symfony\Component\Translation\TranslatableMessage;

class DateRangeActiveFilterFormatter
{
    public function __invoke(FilterData $data)
    {
        /** @var array{from: ?\DateTimeInterface, to: ?\DateTimeInterface} $value */
        $value = $data->getValue();

        $dateFrom = $value['from'];
        $dateTo = $value['to'];

        $dateFormatter = new DateActiveFilterFormatter();
        $skeleton = DateActiveFilterFormatter::DATE_SKELETON;

        $formattedFrom = $dateFrom === null ? null : $dateFormatter->format($dateFrom, $skeleton);
        $formattedTo = $dateTo === null ? null : $dateFormatter->format($dateTo, $skeleton);

        if ($formattedTo === null) {
            return new TranslatableMessage('After %date%', ['%date%' => $formattedFrom], 'KreyuDataTable');
        }

        if ($formattedFrom === null) {
            return new TranslatableMessage('Before %date%', ['%date%' => $formattedTo], 'KreyuDataTable');
        }

        if ($formattedFrom === $formattedTo) {
            return $formattedFrom;
        }

        return $formattedFrom.' - '.$formattedTo;
    }
}
