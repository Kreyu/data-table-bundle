<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a column with value displayed as a date without time.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/date
 */
final class DateColumnType extends AbstractDateTimeColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('format', 'd.m.Y');
    }
}
