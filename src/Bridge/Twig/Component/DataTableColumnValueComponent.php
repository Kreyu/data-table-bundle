<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Twig\Component;

use Kreyu\Bundle\DataTableBundle\Column\View\ColumnViewInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('data-table-column-value', template: '@KreyuDataTable\Component\data_table_column_value.html.twig')]
class DataTableColumnValueComponent
{
    public ColumnViewInterface $view;
}
