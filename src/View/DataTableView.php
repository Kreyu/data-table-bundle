<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\View;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Symfony\Component\Form\FormView;

class DataTableView implements DataTableViewInterface
{
    public function __construct(
        private readonly array $columns,
        private readonly array $filters,
        private readonly PaginationInterface $pagination,
        private readonly FormView $filtersForm,
        private readonly string $sortParameterName,
        private readonly string $pageParameterName,
        private readonly string $perPageParameterName,
    ) {
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getPagination(): PaginationInterface
    {
        return $this->pagination;
    }

    public function getFiltersForm(): FormView
    {
        return $this->filtersForm;
    }

    public function getSortParameterName(): string
    {
        return $this->sortParameterName;
    }

    public function getPageParameterName(): string
    {
        return $this->pageParameterName;
    }

    public function getPerPageParameterName(): string
    {
        return $this->perPageParameterName;
    }
}
