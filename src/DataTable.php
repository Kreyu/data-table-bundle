<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Kreyu\Bundle\DataTableBundle\Exporter\Form\Type\ExportDataType;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
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
     * The sorting data currently applied to the data table.
     */
    private ?SortingData $sortingData = null;

    /**
     * The pagination data currently applied to the data table.
     */
    private ?PaginationData $paginationData = null;

    /**
     * The filtration data currently applied to the data table.
     */
    private ?FiltrationData $filtrationData = null;

    /**
     * The personalization data currently applied to the data table.
     */
    private ?PersonalizationData $personalizationData = null;

    /**
     * The export data currently applied to the data table.
     */
    private ?ExportData $exportData = null;

    /**
     * Lazy-loaded pagination used to retrieve the current page results.
     */
    private null|PaginationInterface $pagination = null;

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

        $this->paginationData = $data;

        $this->resetPagination();
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

        $this->sortingData = $data;

        $this->resetPagination();
    }

    public function filter(FiltrationData $data): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        foreach ($this->config->getFilters() as $filter) {
            $filterData = $data->getFilterData($filter->getName());

            if ($filterData && $filterData->hasValue()) {
                $filter->setData($filterData);
                $filter->apply($this->query, $filterData);
            }
        }

        if ($this->config->isFiltrationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getFiltrationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getFiltrationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $data);
        }

        $this->filtrationData = $data;

        $this->resetPagination();
    }

    public function personalize(PersonalizationData $data): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getPersonalizationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use personalization persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getPersonalizationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use personalization persistence, but does not have a subject.');
            }

            $persistenceAdapter->write($this, $persistenceSubject, $data);
        }

        $this->personalizationData = $data;
    }

    public function isExporting(): bool
    {
        return null !== $this->exportData;
    }

    public function export(ExportData $exportData = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new \RuntimeException('The data table requested to export has exporting feature disabled.');
        }

        $exportData ??= $this->exportData;

        if (null === $exportData) {
            throw new \RuntimeException('Unable to export the data table without an export data. Explicitly pass the export data as the first argument of the "export()" method.');
        }

        $this->exportData = $exportData;

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
        return (bool) $this->filtrationData?->hasActiveFilters();
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
        return $this->pagination ??= $this->query->getPagination();
    }

    public function getSortingData(): ?SortingData
    {
        return $this->sortingData;
    }

    public function getPaginationData(): ?PaginationData
    {
        return $this->paginationData;
    }

    public function getFiltrationData(): ?FiltrationData
    {
        return $this->filtrationData;
    }

    public function getPersonalizationData(): ?PersonalizationData
    {
        return $this->personalizationData;
    }

    public function getExportData(): ?ExportData
    {
        return $this->exportData;
    }

    public function createView(): DataTableView
    {
        if (empty($this->config->getColumns())) {
            throw new \LogicException('The data table has no configured columns.');
        }

        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createView($this);

        $type->buildView($view, $this, $options);

        return $view;
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

        $data = $this->config->getDefaultFiltrationData();

        if ($this->config->isFiltrationPersistenceEnabled()) {
            if (null === $persistenceAdapter = $this->config->getFiltrationPersistenceAdapter()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have an adapter.');
            }

            if (null === $persistenceSubject = $this->config->getFiltrationPersistenceSubject()) {
                throw new \RuntimeException('The data table is configured to use filtration persistence, but does not have a subject.');
            }

            $data = $persistenceAdapter->read($this, $persistenceSubject, $data);
        }

        if (null !== $data) {
            $this->filter($data);
        }
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

    private function resetPagination(): void
    {
        $this->pagination = null;
    }
}
