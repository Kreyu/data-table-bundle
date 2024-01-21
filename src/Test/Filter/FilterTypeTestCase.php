<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use PHPUnit\Framework\MockObject\MockObject;

abstract class FilterTypeTestCase extends FilterIntegrationTestCase
{
    /**
     * @return class-string<FilterTypeInterface>
     */
    abstract protected function getTestedType(): string;

    protected function createFilter(array $options = []): FilterInterface
    {
        return $this->factory->create($this->getTestedType(), $options);
    }

    protected function createNamedFilter(string $name, array $options = []): FilterInterface
    {
        return $this->factory->createNamed($name, $this->getTestedType(), $options);
    }

    protected function createFilterView(FilterInterface $filter, FilterData $data = null, DataTableView $parent = null): FilterView
    {
        return $filter->createView(
            data: $data ?? $this->createFilterDataMock(),
            parent: $parent ?? $this->createDataTableViewMock(),
        );
    }

    protected function createFilterDataMock(): MockObject&FilterData
    {
        return $this->createMock(FilterData::class);
    }

    protected function createDataTableViewMock(): MockObject&DataTableView
    {
        return $this->createMock(DataTableView::class);
    }
}
