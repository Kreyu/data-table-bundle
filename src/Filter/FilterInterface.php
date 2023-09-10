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

    /**
     * @param ProxyQueryInterface|null $query if not given, filter will be applied to the related data table query
     * @param FilterData|null          $data  if not given, filter will be applied using filter data from the related data table
     */
    public function apply(ProxyQueryInterface $query = null, FilterData $data = null): void;

    public function createView(FilterData $data, DataTableView $parent): FilterView;
}
