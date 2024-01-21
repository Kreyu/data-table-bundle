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
            $formOptions = $filter->getConfig()->getOption('form_options');

            $format = $formOptions['input_format'] ?? null;

            if (null === $format) {
                $format = 'Y-m-d H';

                if ($formOptions['with_minutes'] ?? true) {
                    $format .= ':i';
                }

                if ($formOptions['with_seconds'] ?? true) {
                    $format .= ':s';
                }
            }

            return $value->format($format);
        }

        return (string) $value;
    }
}
