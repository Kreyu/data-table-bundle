<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Twig\Component;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnHeaderViewFactoryInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent('data-table-column-header', template: '@KreyuDataTable\Component\data_table_column_header.html.twig')]
class DataTableColumnHeaderComponent
{
    public DataTableViewInterface $dataTable;
    public ColumnInterface $column;

    public function __construct(
        private readonly ColumnHeaderViewFactoryInterface $columnHeaderViewFactory,
    ) {
    }

    #[PostMount]
    public function postMount()
    {
        $columnView = $this->columnHeaderViewFactory->create(
            $this->dataTable,
            $this->column,
        );

        return $columnView->getVariables();
    }
}
