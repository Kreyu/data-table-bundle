<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a column with value displayed as date and time.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/date-time
 */
final class DateTimeColumnType extends AbstractDateTimeColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('format', 'd.m.Y H:i:s');
    }
}
