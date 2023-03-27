<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Extension;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractColumnTypeExtension implements ColumnTypeExtensionInterface
{
    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
