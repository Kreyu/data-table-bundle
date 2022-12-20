<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\View;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Symfony\Component\Form\FormView;

interface DataTableViewInterface
{
    /**
     * @return array<ColumnInterface>
     */
    public function getColumns(): array;

    /**
     * @return array<FilterInterface>
     */
    public function getFilters(): array;

    public function getPagination(): PaginationInterface;

    public function getFiltersForm(): FormView;

    public function getSortParameterName(): string;

    public function getPageParameterName(): string;

    public function getPerPageParameterName(): string;
}
