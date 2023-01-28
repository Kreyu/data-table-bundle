<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectNotFoundException;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class SortingExtension extends AbstractTypeExtension
{
    public function __construct(
        private PersistenceAdapterInterface $persistenceAdapter,
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setSortingEnabled(true)
            ->setSortingPersistenceEnabled(true)
            ->setSortingPersistenceAdapter($this->persistenceAdapter)
        ;

        try {
            $builder->setSortingPersistenceSubject($this->persistenceSubjectProvider->provide());
        } catch (PersistenceSubjectNotFoundException) {}
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}
