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
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormBuilderInterface;
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
            $this->setPersistenceData('pagination', $data);
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
            $this->setPersistenceData('sorting', $data);
        }

        $this->sortingData = $data;

        $this->resetPagination();
    }

    public function filter(FiltrationData|array $data): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        if (is_array($data)) {
            $form = $this->createFiltrationFormBuilder()->getForm();
            $form->submit($data);

            $data = $form->getData();
        }

        foreach ($this->config->getFilters() as $filter) {
            $filterData = $data->getFilterData($filter->getName());

            if ($filterData && $filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        if ($this->config->isFiltrationPersistenceEnabled()) {
            $this->setPersistenceData('filtration', $data);
        }

        $this->filtrationData = $data;

        $this->resetPagination();
    }

    public function personalize(PersonalizationData|array $data): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        if (is_array($data)) {
            $form = $this->createPersonalizationFormBuilder()->getForm();
            $form->submit($data);

            $data = $form->getData();
        }

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            $this->setPersistenceData('personalization', $data);
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

        if (null === $exportData ??= $this->exportData) {
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
        return (bool) $this->filtrationData?->hasActiveFilters();
    }

    public function handleRequest(mixed $request): void
    {
        if (null === $requestHandler = $this->config->getRequestHandler()) {
            throw new \RuntimeException(sprintf('The "handleRequest cannot be used on data tables without configured request handler.', __METHOD__));
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
        // $this->filtrationData?->appendMissingFilters($this->getConfig()->getFilters());

        return $this->filtrationData;
    }

    public function getPersonalizationData(): ?PersonalizationData
    {
        $this->personalizationData ??= PersonalizationData::fromDataTable($this);
        $this->personalizationData->appendMissingColumns($this->getConfig()->getColumns());

        return $this->personalizationData;
    }

    public function getExportData(): ?ExportData
    {
        return $this->exportData;
    }

    public function createFiltrationFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isFiltrationEnabled()) {
            throw new \RuntimeException('The data table has filtration feature disabled.');
        }

        if (null === $this->config->getFiltrationFormFactory()) {
            throw new \RuntimeException('The data table has no configured filtration form factory.');
        }

        return $this->config->getFiltrationFormFactory()->createNamedBuilder(
            name: $this->getConfig()->getFiltrationParameterName(),
            type: FiltrationDataType::class,
            options: [
                'data_table' => $view,
                'filters' => $this->config->getFilters(),
            ],
        );
    }

    public function createPersonalizationFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isPersonalizationEnabled()) {
            throw new \RuntimeException('The data table has personalization feature disabled.');
        }

        if (null === $this->config->getPersonalizationFormFactory()) {
            throw new \RuntimeException('The data table has no configured personalization form factory.');
        }

        return $this->config->getFiltrationFormFactory()->createNamedBuilder(
            name: $this->getConfig()->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'data_table' => $view,
            ],
        );
    }

    public function createView(): DataTableView
    {
        if (empty($this->config->getColumns())) {
            throw new \LogicException('The data table has no configured columns.');
        }

        if (null === $this->paginationData) {
            $this->paginate($this->getDefaultPaginationData());
        }

        if (null === $this->sortingData) {
            $this->sort($this->getDefaultSortingData());
        }

        if (null === $this->filtrationData) {
            $this->filter($this->getDefaultFiltrationData());
        }

        if (null === $this->personalizationData && $personalizationData = $this->getDefaultPersonalizationData()) {
            $this->personalize($personalizationData);
        }

        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createView($this);

        $type->buildView($view, $this, $options);

        return $view;
    }

    private function getDefaultPaginationData(): ?PaginationData
    {
        if (!$this->config->isPaginationEnabled()) {
            return null;
        }

        $data = $this->config->getDefaultPaginationData();

        if ($this->config->isPaginationPersistenceEnabled()) {
            $data ??= $this->getPersistenceData('pagination');
        }

        $data ??= new PaginationData(
            page: PaginationInterface::DEFAULT_PAGE,
            perPage: PaginationInterface::DEFAULT_PER_PAGE,
        );

        return $data;
    }

    private function getDefaultSortingData(): ?SortingData
    {
        if (!$this->config->isSortingEnabled()) {
            return null;
        }

        $data = $this->config->getDefaultSortingData();

        if ($this->config->isSortingPersistenceEnabled()) {
            $data ??= $this->getPersistenceData('sorting');
        }

        return $data;
    }

    private function getDefaultFiltrationData()
    {
        if (!$this->config->isFiltrationEnabled()) {
            return null;
        }

        $data = $this->config->getDefaultFiltrationData();

        if ($this->config->isFiltrationPersistenceEnabled()) {
            $data ??= $this->getPersistenceData('filtration');
        }

        return $data;
    }

    private function getDefaultPersonalizationData(): ?PersonalizationData
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return null;
        }

        $data = $this->config->getDefaultPersonalizationData();

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            $data ??= $this->getPersistenceData('personalization');
        }

        return $data;
    }

    private function isPersistenceEnabled(string $context): bool
    {
        return match ($context) {
            'sorting' => $this->config->isSortingPersistenceEnabled(),
            'pagination' => $this->config->isPaginationPersistenceEnabled(),
            'filtration' => $this->config->isFiltrationPersistenceEnabled(),
            'personalization' => $this->config->isPersonalizationPersistenceEnabled(),
            default => throw new \RuntimeException('Given persistence context is not supported.'),
        };
    }

    private function getPersistenceData(string $context): mixed
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new \RuntimeException(sprintf('The data table has %s persistence disabled.', $context));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);
        $persistenceSubject = $this->getPersistenceSubject($context);

        return $persistenceAdapter->read($this, $persistenceSubject);
    }

    private function setPersistenceData(string $context, mixed $data): void
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new \RuntimeException(sprintf('The data table has %s persistence disabled.', $context));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);
        $persistenceSubject = $this->getPersistenceSubject($context);

        $persistenceAdapter->write($this, $persistenceSubject, $data);
    }

    private function getPersistenceAdapter(string $context): PersistenceAdapterInterface
    {
        $adapter = match ($context) {
            'sorting' => $this->config->getSortingPersistenceAdapter(),
            'pagination' => $this->config->getPaginationPersistenceAdapter(),
            'filtration' => $this->config->getFiltrationPersistenceAdapter(),
            'personalization' => $this->config->getPersonalizationPersistenceAdapter(),
            default => throw new \RuntimeException('Given persistence context is not supported.'),
        };

        if (null === $adapter) {
            throw new \RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have an adapter.', $context));
        }

        return $adapter;
    }

    private function getPersistenceSubject(string $context): PersistenceSubjectInterface
    {
        $subject = match ($context) {
            'sorting' => $this->config->getSortingPersistenceSubject(),
            'pagination' => $this->config->getPaginationPersistenceSubject(),
            'filtration' => $this->config->getFiltrationPersistenceSubject(),
            'personalization' => $this->config->getPersonalizationPersistenceSubject(),
            default => throw new \RuntimeException('Given persistence context is not supported.'),
        };

        if (null === $subject) {
            throw new \RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have a subject.', $context));
        }

        return $subject;
    }

    private function resetPagination(): void
    {
        $this->pagination = null;
    }
}
