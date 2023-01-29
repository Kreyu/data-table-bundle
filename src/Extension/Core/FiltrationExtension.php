<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectNotFoundException;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\Form\FormFactoryInterface;

class FiltrationExtension extends AbstractTypeExtension
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private PersistenceAdapterInterface $persistenceAdapter,
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setFiltrationFormFactory($this->formFactory);

        if ($builder->isFiltrationEnabled() && $builder->isFiltrationPersistenceEnabled()) {
            $builder->setFiltrationPersistenceAdapter($this->persistenceAdapter);

            try {
                $builder->setFiltrationPersistenceSubject($this->persistenceSubjectProvider->provide());
            } catch (PersistenceSubjectNotFoundException) {}
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}