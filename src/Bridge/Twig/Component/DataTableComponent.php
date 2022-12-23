<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Twig\Component;

use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('data-table', template: '@KreyuDataTable\Component\data_table.html.twig')]
class DataTableComponent
{
    public DataTableViewInterface $dataTable;
}
