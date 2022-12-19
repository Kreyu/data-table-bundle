<?php
declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Symfony;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Factory\DataTableFactoryInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Contracts\Service\Attribute\Required;

trait DataTableControllerTrait
{
    private null|DataTableFactoryInterface $dataTableFactory = null;

    public function createDataTable(string $type, mixed $data, array $options = []): DataTableInterface
    {
        if (null === $this->dataTableFactory) {
            throw new \LogicException(sprintf('You cannot use the "%s" method on controller without data table factory.', __METHOD__));
        }

        return $this->dataTableFactory->create($type, $data, $options);
    }

    #[Required]
    public function setDataTableFactory(DataTableFactoryInterface $dataTableFactory): void
    {
        $this->dataTableFactory = $dataTableFactory;
    }
}