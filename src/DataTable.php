<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Persistence\FilterPersisterSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationType;
use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
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
    private FormInterface $personalizationForm;
    private PersonalizationData $personalizationData;

    public function __construct(
        private readonly string $name,
        private readonly ProxyQueryInterface $query,
        private readonly array $columns,
        private readonly array $filters,
        private readonly FormFactoryInterface $formFactory,
        private readonly ?FilterPersisterInterface $filterPersister,
        private readonly ?FilterPersisterSubjectInterface $filterPersisterSubject,
        private readonly ?PersonalizationPersisterInterface $personalizationPersister,
        private readonly ?PersonalizationPersisterSubjectInterface $personalizationPersisterSubject,
    ) {
        $this->filtersForm = $this->buildFiltersForm();

        $this->personalizationData = new PersonalizationData($this->columns);
        $this->personalizationForm = $this->buildPersonalizationForm();

        if ($this->filterPersister && $this->filterPersisterSubject) {
            $filters = $this->filterPersister->get($this->filterPersisterSubject, $this);

            if (!empty($filters)) {
                $this->filter($filters);
            }
        }

        if ($this->personalizationPersister && $this->personalizationPersisterSubject) {
            $personalization = $this->personalizationPersister->get($this->personalizationPersisterSubject, $this);

            $this->personalize($personalization->toFormData());
        }
    }

    public function getName(): string
    {
        return $this->name;
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
        $this->filtersForm = $this->buildFiltersForm();
        $this->filtersForm->submit($data);

        foreach ($this->filters as $filter) {
            $filterFormName = $filter->getFormName();

            $filterData = FilterData::fromArray($data[$filterFormName] ?? []);

            if ($filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        if (null !== $this->filterPersister && null !== $this->filterPersisterSubject) {
            $this->filterPersister->save($this->filterPersisterSubject, $this, $data);
        }
    }

    public function sort(string $field, string $direction): void
    {
        $this->query->sort($field, $direction);
    }

    public function paginate(int $page, int $perPage): void
    {
        $this->query->paginate($page, $perPage);
    }

    public function personalize(array $data): void
    {
        $this->personalizationForm = $this->buildPersonalizationForm();
        $this->personalizationForm->submit($data);

        $personalizationData = $this->personalizationForm->getViewData();

        if (null !== $this->personalizationPersister && null !== $this->personalizationPersisterSubject) {
            $this->personalizationPersister->save($this->personalizationPersisterSubject, $this, $personalizationData);
        }
    }

    public function getFiltersForm(): FormInterface
    {
        return $this->filtersForm;
    }

    public function getPersonalizationForm(): FormInterface
    {
        return $this->personalizationForm;
    }

    public function handleRequest(Request $request): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $parameters = $request->query->all();

        $filterFormName = $this->getFilterFormName();

        $filters = $propertyAccessor->getValue($parameters, "[$filterFormName]") ?? [];

        if (!empty($filters)) {
            $this->filter($filters);
        }

        $sortParameterName = $this->getSortParameterName();

        $sortField = $propertyAccessor->getValue($parameters, "[$sortParameterName][field]");
        $sortDirection = $propertyAccessor->getValue($parameters, "[$sortParameterName][direction]");

        if (null !== $sortField && null !== $sortDirection) {
            $this->sort($sortField, $sortDirection);
        }

        $personalization = $request->request->all($this->getPersonalizationFormName());

        if (!empty($personalization)) {
            $this->personalize($personalization);
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

    public function getPersonalizationFormName(): string
    {
        return $this->getParameterName('personalization');
    }

    public function getPersonalizationData(): ?PersonalizationData
    {
        return $this->personalizationData;
    }

    public function createView(): DataTableViewInterface
    {
        return new DataTableView(
            columns: $this->columns,
            filters: $this->filters,
            pagination: $this->query->getPagination(),
            filtersForm: $this->filtersForm->createView(),
            personalizationForm: $this->personalizationForm->createView(),
            sortParameterName: $this->getSortParameterName(),
            pageParameterName: $this->getPageParameterName(),
            perPageParameterName: $this->getPerPageParameterName(),
            personalizationFormName: $this->getPersonalizationFormName(),
            personalizationData: $this->personalizationData,
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

    private function buildPersonalizationForm(): FormInterface
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            name: $this->getPersonalizationFormName(),
            type: PersonalizationType::class,
            options: [
                'csrf_protection' => false,
            ],
        );

        $formBuilder->setMethod('POST');
        $formBuilder->setData($this->getPersonalizationData());

        return $formBuilder->getForm();
    }

    private function getParameterName(string $prefix): string
    {
        return implode('_', array_filter([$prefix, $this->name]));
    }
}
