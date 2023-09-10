<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;

class DataTableView
{
    public array $vars = [];

    public ?HeaderRowView $headerRow = null;

    public ?HeaderRowView $nonPersonalizedHeaderRow = null;

    /**
     * @var iterable<ValueRowView>
     */
    public iterable $valueRows = [];

    public ?PaginationView $pagination = null;

    /**
     * @var array<FilterView>
     */
    public array $filters = [];

    /**
     * @var array<ActionView>
     */
    public array $actions = [];
}
