<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface DataTableInterface
{
    public const PAGE_PARAMETER = 'page';
    public const PER_PAGE_PARAMETER = 'limit';
    public const SORT_PARAMETER = 'sort';
    public const FILTER_PARAMETER = 'filter';

    /**
     * @return array<ColumnInterface>
     */
    public function getColumns(): array;

    /**
     * @return array<FilterInterface>
     */
    public function getFilters(): array;

    /**
     * @return FormInterface
     */
    public function getFiltersForm(): FormInterface;

    public function sort(string $field, string $direction): void;

    public function filter(array $data): void;

    public function paginate(int $page, int $perPage): void;

    public function handleRequest(Request $request): void;

    public function createView(): DataTableViewInterface;
}