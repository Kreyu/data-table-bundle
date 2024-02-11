<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DatePeriodColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'separator' => $options['separator'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('separator', ' - ')
            ->setAllowedTypes('separator', ['null', 'string'])
            ->setInfo('separator', 'A string used to visually separate start and end dates.')
        ;
    }

    public function getParent(): ?string
    {
        return DateTimeColumnType::class;
    }
}
