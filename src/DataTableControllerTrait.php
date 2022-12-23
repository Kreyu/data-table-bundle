<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Factory\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait DataTableControllerTrait
{
    private null|DataTableFactoryInterface $dataTableFactory = null;

    /**
     * @param class-string<DataTableTypeInterface> $typeClass
     */
    public function createDataTable(string $typeClass, mixed $data, array $options = []): DataTableInterface
    {
        if (null === $this->dataTableFactory) {
            throw new \LogicException(sprintf('You cannot use the "%s" method on controller without data table factory.', __METHOD__));
        }

        return $this->dataTableFactory->create($typeClass, $data, $options);
    }

    /**
     * @param class-string<DataTableTypeInterface> $typeClass
     */
    public function createNamedDataTable(string $name, string $typeClass, mixed $data, array $options = []): DataTableInterface
    {
        if (null === $this->dataTableFactory) {
            throw new \LogicException(sprintf('You cannot use the "%s" method on controller without data table factory.', __METHOD__));
        }

        return $this->dataTableFactory->createNamed($name, $typeClass, $data, $options);
    }

    #[Required]
    public function setDataTableFactory(DataTableFactoryInterface $dataTableFactory): void
    {
        $this->dataTableFactory = $dataTableFactory;
    }
}
