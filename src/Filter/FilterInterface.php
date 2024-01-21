<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface FilterInterface
{
    public function getName(): string;

    public function getConfig(): FilterConfigInterface;

    public function getDataTable(): DataTableInterface;

    public function setDataTable(DataTableInterface $dataTable): static;

    public function getFormName(): string;

    public function getFormOptions(): array;

    public function getQueryPath(): string;

    public function handle(ProxyQueryInterface $query, FilterData $data): void;

    public function createView(FilterData $data, DataTableView $parent): FilterView;
}
