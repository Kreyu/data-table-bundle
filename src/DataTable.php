<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Kreyu\Bundle\DataTableBundle\Exporter\Form\Type\ExportDataType;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FilterDataType;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FiltrationDataType;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
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

    /**
     * Lazy-loaded form used to apply export criteria to the export feature.
     */
    private null|FormInterface $exportForm = null;

    public function __construct(
        private ProxyQueryInterface $query,
        private DataTableConfigInterface $config,
    ) {
        $this->initializeSorting();
        $this->initializePagination();
        $this->initializePersonalization();
    }

    public function getConfig(): DataTableConfigInterface
    {
        return $this->config;
    }

    public function paginate(PaginationData $data): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $this->query->paginate($data);

        if ($this->config->isPaginationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPaginationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use pagination persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPaginationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use pagination persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $data);
        }
    }

    public function sort(SortingData $data): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $this->query->sort($data);

        if ($this->config->isSortingPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getSortingPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use sorting persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getSortingPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use sorting persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $data);
        }
    }

    public function filter(FiltrationData $data): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        foreach ($this->config->getFilters() as $filter) {
            $filterData = $data->getFilter($filter);

            if ($filterData && $filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        $this->getFiltrationForm()->setData($data);

        if ($this->config->isFiltrationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getFiltrationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getFiltrationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $data);
        }
    }

    public function personalize(PersonalizationData $data): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $this->getPersonalizationForm()->setData($data);

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPersonalizationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use personalization persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPersonalizationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use personalization persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $data);
        }
    }

    public function isExporting(): bool
    {
        return $this->getExportForm()->isSubmitted();
    }

    public function export(ExportData $exportData = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new \RuntimeException('The data table requested to export has exporting feature disabled.');
        }

        /** @var ExportData $exportData */
        $exportData ??= $this->getExportForm()->getData();

        if (null === $exportData) {
            throw new \RuntimeException('Unable to export the data table without an export data. Explicitly pass the export data as the first argument of the "export()" method.');
        }

        $dataTable = clone $this;

        if (ExportStrategy::INCLUDE_ALL === $exportData->strategy) {
            $dataTable->paginate(new PaginationData(perPage: null));
        }

        if (!$exportData->includePersonalization) {
            $dataTable->personalize(PersonalizationData::fromDataTable($this));
        }

        $filename = $exportData->filename ?? $this->getConfig()->getName();

        return $exportData->exporter->export($dataTable->createView(), $filename);
    }

    public function hasActiveFilters(): bool
    {
        return $this->getFiltrationForm()->getData()->hasActiveFilters();
    }

    public function handleRequest(mixed $request): void
    {
        if (null === $requestHandler = $this->config->getRequestHandler()) {
            throw new \RuntimeException(sprintf('%s cannot be used on data tables without configured request handler.', __METHOD__));
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

    public function getExportForm(): FormInterface
    {
        return $this->exportForm ??= $this->buildExportForm();
    }

    public function createView(): DataTableView
    {
        if (empty($this->config->getColumns())) {
            throw new \LogicException('The data table has no configured columns.');
        }

        if (null === $this->getFiltrationForm()->getData()) {
            $this->initializeFiltration();
        }

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
                'filters' => $this->config->getFilters(),
            ],
        );

        foreach ($this->config->getFilters() as $filter) {
            $formBuilder->add(
                $filter->getFormName(),
                FilterDataType::class,
                $filter->getFormOptions() + [
                    'getter' => fn (FiltrationData $data) => $data->getFilter($filter),
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

    private function buildExportForm(): FormInterface
    {
        $formBuilder = $this->config->getExportFormFactory()->createNamedBuilder(
            name: $this->config->getExportParameterName(),
            type: ExportDataType::class,
            options: [
                'method' => 'POST',
                'exporters' => $this->config->getExporters(),
                'default_filename' => $this->getConfig()->getName(),
            ],
        );

        return $formBuilder->getForm();
    }

    private function initializePagination(): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $data = $this->config->getDefaultPaginationData() ?? new PaginationData(PaginationInterface::DEFAULT_PAGE, PaginationInterface::DEFAULT_PER_PAGE);

        if ($this->config->isPaginationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPaginationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use pagination persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPaginationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use pagination persistence, but does not have a subject.');
            }

            $data = $persistenceAdapter->read($this, $persistenceSubject, $data);
        }

        $this->paginate($data);
    }

    private function initializeSorting(): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $data = $this->config->getDefaultSortingData() ?? new SortingData();

        if ($this->config->isSortingPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getSortingPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use sorting persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getSortingPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use sorting persistence, but does not have a subject.');
            }

            $data = $persistenceAdapter->read($this, $persistenceSubject, $data);
        }

        $this->sort($data);
    }

    private function initializeFiltration(): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        $data = $this->config->getDefaultFiltrationData() ?? new FiltrationData();

        if ($this->config->isFiltrationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getFiltrationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getFiltrationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have a subject.');
            }

            $data = $persistenceAdapter->read($this, $persistenceSubject, $data);
        }

        $this->filter($data);
    }

    private function initializePersonalization(): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $personalizationData = $this->config->getDefaultPersonalizationData() ?? PersonalizationData::fromDataTable($this);

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPersonalizationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use personalization persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPersonalizationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use personalization persistence, but does not have a subject.');
            }

            $personalizationData = $persistenceAdapter->read($this, $persistenceSubject, $personalizationData);
        }

        $this->personalize($personalizationData);
    }
}
