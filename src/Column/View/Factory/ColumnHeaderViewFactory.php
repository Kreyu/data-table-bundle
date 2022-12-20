<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\View\Factory;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\ColumnView;
use Kreyu\Bundle\DataTableBundle\Column\View\ColumnViewInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;

class ColumnHeaderViewFactory implements ColumnHeaderViewFactoryInterface
{
    public function create(DataTableViewInterface $dataTable, ColumnInterface $column): ColumnViewInterface
    {
        $options = $column->getOptions();
        $options['label'] ??= $column->getName();

        $view = new ColumnView($options);
        $view->setVariable('data_table', $dataTable);

        $column->getType()->buildHeaderView($view);

        return $view;
    }
}
