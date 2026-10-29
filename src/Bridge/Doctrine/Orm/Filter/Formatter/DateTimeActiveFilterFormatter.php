<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class DateTimeActiveFilterFormatter
{
    public function __invoke(FilterData $data, FilterInterface $filter): string
    {
        $value = $data->getValue();

        if ($value instanceof \DateTimeInterface) {
            /** @var array{input_format?: string|null, with_minutes?: bool, with_seconds?: bool} $formOptions */
            $formOptions = $filter->getConfig()->getOption('form_options');

            $inputFormat = $formOptions['input_format'] ?? null;

            if ($inputFormat !== null) {
                return $value->format($inputFormat);
            }

            $withMinutes = $formOptions['with_minutes'] ?? true;
            $withSeconds = $formOptions['with_seconds'] ?? true;

            $timeSkeleton = match (true) {
                $withSeconds => 'jms',
                $withMinutes => 'jm',
                default => 'j',
            };

            return (new DateActiveFilterFormatter())->format(
                $value,
                DateActiveFilterFormatter::DATE_SKELETON.$timeSkeleton,
            );
        }

        return (string) $value;
    }
}
