<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;

class DataTableView
{
    public array $vars = [];

    public HeaderRowView $headerRow;

    public HeaderRowView $nonPersonalizedHeaderRow;

    /**
     * @var array<ValueRowView>
     */
    public array $valueRows = [];

    public PaginationView $pagination;

    /**
     * @var array<FilterView>
     */
    public array $filters = [];

    /**
     * @var array<ActionView>
     */
    public array $actions = [];
}
