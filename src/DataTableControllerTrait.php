<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait DataTableControllerTrait
{
    private null|DataTableFactoryInterface $dataTableFactory = null;

    /**
     * @param class-string<DataTableTypeInterface> $type
     */
    protected function createDataTable(string $type, ?ProxyQueryInterface $query = null, array $options = []): DataTableInterface
    {
        if (null === $this->dataTableFactory) {
            throw new \LogicException(sprintf('You cannot use the "%s" method on controller without data table factory.', __METHOD__));
        }

        return $this->dataTableFactory->create($type, $query, $options);
    }

    /**
     * @param class-string<DataTableTypeInterface> $type
     */
    protected function createNamedDataTable(string $name, string $type, ?ProxyQueryInterface $query = null, array $options = []): DataTableInterface
    {
        if (null === $this->dataTableFactory) {
            throw new \LogicException(sprintf('You cannot use the "%s" method on controller without data table factory.', __METHOD__));
        }

        return $this->dataTableFactory->createNamed($name, $type, $query, $options);
    }

    protected function createDataTableBuilder(?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface
    {
        if (null === $this->dataTableFactory) {
            throw new \LogicException(sprintf('You cannot use the "%s" method on controller without data table factory.', __METHOD__));
        }

        return $this->dataTableFactory->createBuilder(DataTableType::class, $query, $options);
    }

    #[Required]
    public function setDataTableFactory(?DataTableFactoryInterface $dataTableFactory): void
    {
        $this->dataTableFactory = $dataTableFactory;
    }
}
