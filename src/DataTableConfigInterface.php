<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;

interface DataTableConfigInterface
{
    public function getName(): string;

    public function getType(): ResolvedDataTableTypeInterface;

    public function getOptions(): array;

    /**
     * @return array<ColumnInterface>
     */
    public function getColumns(): array;

    /**
     * @return array<FilterInterface>
     */
    public function getFilters(): array;

    /**
     * @return array<ExporterInterface>
     */
    public function getExporters(): array;

    public function isExportingEnabled(): bool;

    public function getExportFormFactory(): ?FormFactoryInterface;

    public function isPersonalizationEnabled(): bool;

    public function isPersonalizationPersistenceEnabled(): bool;

    public function getPersonalizationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getPersonalizationPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getPersonalizationFormFactory(): ?FormFactoryInterface;

    public function isFiltrationEnabled(): bool;

    public function isFiltrationPersistenceEnabled(): bool;

    public function getFiltrationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getFiltrationPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getFiltrationFormFactory(): ?FormFactoryInterface;

    public function isSortingEnabled(): bool;

    public function isSortingPersistenceEnabled(): bool;

    public function getSortingPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getSortingPersistenceSubject(): ?PersistenceSubjectInterface;

    public function isPaginationEnabled(): bool;

    public function isPaginationPersistenceEnabled(): bool;

    public function getPaginationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getPaginationPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getRequestHandler(): ?RequestHandlerInterface;

    public function getPageParameterName(): string;

    public function getPerPageParameterName(): string;

    public function getSortParameterName(): string;

    public function getFiltrationParameterName(): string;

    public function getPersonalizationParameterName(): string;

    public function getExportParameterName(): string;
}
