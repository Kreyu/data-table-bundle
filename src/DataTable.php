<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FilterDataType;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FiltrationDataType;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use RuntimeException;
use Symfony\Component\Form\FormInterface;

class DataTable implements DataTableInterface
{
    /**
     * Lazy-loaded form used to apply filtration criteria to the data table.
     */
    private null|FormInterface $filtrationForm = null;

    /**
     * Lazy-loaded form used to apply personalization criteria to the data table.
     */
    private null|FormInterface $personalizationForm = null;

    public function __construct(
        private ProxyQueryInterface $query,
        private DataTableConfigInterface $config,
    ) {
        $this->initializeSorting();
        $this->initializeFiltration();
        $this->initializePagination();
        $this->initializePersonalization();
    }

    public function getConfig(): DataTableConfigInterface
    {
        return $this->config;
    }

    public function paginate(PaginationData $paginationData): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $this->query->paginate($paginationData);

        if ($this->config->isPaginationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPaginationPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use pagination persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPaginationPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use pagination persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $paginationData);
        }
    }

    public function sort(SortingData $sortingData): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $this->query->sort($sortingData);

        if ($this->config->isSortingPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getSortingPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use sorting persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getSortingPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use sorting persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $sortingData);
        }
    }

    public function filter(FiltrationData $filtrationData): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        foreach ($this->config->getFilters() as $filter) {
            $filterData = $filtrationData->getFilterData($filter);

            if ($filterData && $filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        $this->getFiltrationForm()->setData($filtrationData);

        if ($this->config->isFiltrationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getFiltrationPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use filtration persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getFiltrationPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use filtration persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $filtrationData);
        }
    }

    public function personalize(PersonalizationData $personalizationData): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $this->getPersonalizationForm()->setData($personalizationData);

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPersonalizationPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use personalization persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPersonalizationPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use personalization persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $personalizationData);
        }
    }

    public function handleRequest(mixed $request): void
    {
        if (null === $requestHandler = $this->config->getRequestHandler()) {
            throw new RuntimeException(sprintf('%s cannot be used on data tables without configured request handler.', __METHOD__));
        }

        $requestHandler->handle($this, $request);
    }

    public function getPagination(): PaginationInterface
    {
        return $this->query->getPagination();
    }

    public function getFiltrationForm(): FormInterface
    {
        return $this->filtrationForm ??= $this->buildFiltrationForm();
    }

    public function getPersonalizationForm(): FormInterface
    {
        return $this->personalizationForm ??= $this->buildPersonalizationForm();
    }

    public function createView(): DataTableView
    {
        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createView($this);

        $type->buildView($view, $this, $options);

        return $view;
    }

    private function buildFiltrationForm(): FormInterface
    {
        $formBuilder = $this->config->getFiltrationFormFactory()->createNamedBuilder(
            name: $this->config->getFiltrationParameterName(),
            type: FiltrationDataType::class,
            options: [
                'method' => 'GET',
                'csrf_protection' => false,
                'filters' => $this->config->getFilters(),
            ],
        );

        foreach ($this->config->getFilters() as $filter) {
            $formBuilder->add(
                $filter->getFormName(),
                FilterDataType::class,
                $filter->getFormOptions() + [
                    'getter' => function (FiltrationData $data, FormInterface $form) use ($filter) {
                        return $data->getFilterData($filter);
                    }
                ],
            );
        }

        return $formBuilder->getForm();
    }

    private function buildPersonalizationForm(): FormInterface
    {
        $formBuilder = $this->config->getPersonalizationFormFactory()->createNamedBuilder(
            name: $this->config->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'method' => 'POST',
            ],
        );

        return $formBuilder->getForm();
    }

    private function initializePagination(): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $paginationData = new PaginationData(1, 25);

        if ($this->config->isPaginationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPaginationPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use pagination persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPaginationPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use pagination persistence, but does not have a subject.');
            }

            $paginationData = $persistenceAdapter->read($this, $persistenceSubject, $paginationData);
        }

        $this->paginate($paginationData);
    }

    private function initializeSorting(): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $sortingData = new SortingData();

        if ($this->config->isSortingPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getSortingPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use sorting persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getSortingPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use sorting persistence, but does not have a subject.');
            }

            $sortingData = $persistenceAdapter->read($this, $persistenceSubject, $sortingData);
        }

        if (null === $sortingData) {
            return;
        }

        $this->sort($sortingData);
    }

    private function initializeFiltration(): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        $filtrationData = new FiltrationData();

        if ($this->config->isFiltrationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getFiltrationPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use filtration persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getFiltrationPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use filtration persistence, but does not have a subject.');
            }

            $filtrationData = $persistenceAdapter->read($this, $persistenceSubject, $filtrationData);
        }

        $this->filter($filtrationData);
    }

    private function initializePersonalization(): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $personalizationData = new PersonalizationData($this->getConfig()->getColumns());

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPersonalizationPersistenceAdapter()) {
                throw new RuntimeException('The data table is configured to use personalization persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPersonalizationPersistenceSubject()) {
                throw new RuntimeException('The data table is configured to use personalization persistence, but does not have a subject.');
            }

            $personalizationData = $persistenceAdapter->read($this, $persistenceSubject, $personalizationData);
        }

        $this->personalize($personalizationData);
    }
}
