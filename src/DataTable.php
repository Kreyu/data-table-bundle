<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Form\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableView;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DataTable implements DataTableInterface
{
    private FormInterface $filtersForm;

    public function __construct(
        private readonly null|string $name,
        private readonly ProxyQueryInterface $query,
        private readonly array $columns,
        private readonly array $filters,
        private readonly FormFactoryInterface $formFactory,
    ) {
        $this->filtersForm = $this->buildFiltersForm();
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function filter(array $data): void
    {
        foreach ($this->filters as $filter) {
            $filterFormName = $filter->getFormName();

            $filterData = FilterData::fromArray($data[$filterFormName] ?? []);

            if ($filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        $this->filtersForm->submit($data);
    }

    public function sort(string $field, string $direction): void
    {
        $this->query->sort($field, $direction);
    }

    public function paginate(int $page, int $perPage): void
    {
        $this->query->paginate($page, $perPage);
    }

    public function getFiltersForm(): FormInterface
    {
        return $this->filtersForm;
    }

    public function handleRequest(Request $request): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $parameters = $request->query->all();

        $filterFormName = $this->getFilterFormName();

        $filters = $propertyAccessor->getValue($parameters, "[$filterFormName]") ?? [];

        $this->filter($filters);

        $sortParameterName = $this->getSortParameterName();

        $sortField = $propertyAccessor->getValue($parameters, "[$sortParameterName][field]");
        $sortDirection = $propertyAccessor->getValue($parameters, "[$sortParameterName][direction]");

        if (null !== $sortField && null !== $sortDirection) {
            $this->sort($sortField, $sortDirection);
        }

        $pageParameterName = $this->getPageParameterName();
        $perPageParameterName = $this->getPerPageParameterName();

        $page = $propertyAccessor->getValue($parameters, "[$pageParameterName]") ?? 1;
        $perPage = $propertyAccessor->getValue($parameters, "[$perPageParameterName]") ?? 25;

        $this->paginate((int) $page, (int) $perPage);
    }

    public function getPageParameterName(): string
    {
        return $this->getParameterName(DataTableInterface::PAGE_PARAMETER);
    }

    public function getPerPageParameterName(): string
    {
        return $this->getParameterName(DataTableInterface::PER_PAGE_PARAMETER);
    }

    public function getSortParameterName(): string
    {
        return $this->getParameterName(DataTableInterface::SORT_PARAMETER);
    }

    public function getFilterFormName(): string
    {
        return $this->getParameterName(DataTableInterface::FILTER_PARAMETER);
    }

    public function createView(): DataTableViewInterface
    {
        return new DataTableView(
            columns: $this->columns,
            filters: $this->filters,
            pagination: $this->query->getPagination(),
            filtersForm: $this->filtersForm->createView(),
            sortParameterName: $this->getSortParameterName(),
            pageParameterName: $this->getPageParameterName(),
            perPageParameterName: $this->getPerPageParameterName(),
        );
    }

    private function buildFiltersForm(): FormInterface
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            name: $this->getFilterFormName(),
            options: [
                'csrf_protection' => false,
            ],
        );

        $formBuilder->setMethod('GET');

        foreach ($this->filters as $filter) {
            $formBuilder->add($filter->getFormName(), FilterType::class, $filter->getFormOptions());
        }

        return $formBuilder->getForm();
    }

    private function getParameterName(string $prefix): string
    {
        return implode('_', array_filter([$prefix, $this->name]));
    }
}
