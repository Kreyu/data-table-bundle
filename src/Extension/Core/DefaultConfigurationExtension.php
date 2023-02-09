<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectNotFoundException;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DefaultConfigurationExtension extends AbstractTypeExtension
{
    public function __construct(
        private array $defaults,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setColumnFactory($this->defaults['column_factory'])
            ->setRequestHandler($this->defaults['request_handler'])
        ;

        $builder
            ->setSortingEnabled($this->defaults['sorting']['enabled'])
            ->setSortingPersistenceEnabled($this->defaults['sorting']['persistence_enabled'])
            ->setSortingPersistenceAdapter($this->defaults['sorting']['persistence_adapter'])
            ->setSortingPersistenceSubject($this->getPersistenceSubject($this->defaults['sorting']['persistence_subject_provider']))
        ;

        $builder
            ->setPaginationEnabled($this->defaults['pagination']['enabled'])
            ->setPaginationPersistenceEnabled($this->defaults['pagination']['persistence_enabled'])
            ->setPaginationPersistenceAdapter($this->defaults['pagination']['persistence_adapter'])
            ->setPaginationPersistenceSubject($this->getPersistenceSubject($this->defaults['pagination']['persistence_subject_provider']))
        ;

        $builder
            ->setFiltrationEnabled($this->defaults['filtration']['enabled'])
            ->setFiltrationPersistenceEnabled($this->defaults['filtration']['persistence_enabled'])
            ->setFiltrationPersistenceAdapter($this->defaults['filtration']['persistence_adapter'])
            ->setFiltrationPersistenceSubject($this->getPersistenceSubject($this->defaults['filtration']['persistence_subject_provider']))
            ->setFiltrationFormFactory($this->defaults['filtration']['form_factory'])
            ->setFilterFactory($this->defaults['filtration']['filter_factory'])
        ;

        $builder
            ->setPersonalizationEnabled($this->defaults['personalization']['enabled'])
            ->setPersonalizationPersistenceEnabled($this->defaults['personalization']['persistence_enabled'])
            ->setPersonalizationPersistenceAdapter($this->defaults['personalization']['persistence_adapter'])
            ->setPersonalizationPersistenceSubject($this->getPersistenceSubject($this->defaults['personalization']['persistence_subject_provider']))
            ->setPersonalizationFormFactory($this->defaults['personalization']['form_factory'])
        ;

        $builder
            ->setExportingEnabled($this->defaults['exporting']['enabled'])
            ->setExportFormFactory($this->defaults['exporting']['form_factory'])
            ->setExporterFactory($this->defaults['exporting']['exporter_factory'])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }

    private function getPersistenceSubject(?PersistenceSubjectProviderInterface $persistenceSubjectProvider): ?PersistenceSubjectInterface
    {
        try {
            return $persistenceSubjectProvider?->provide();
        } catch (PersistenceSubjectNotFoundException) {
            return null;
        }
    }
}
