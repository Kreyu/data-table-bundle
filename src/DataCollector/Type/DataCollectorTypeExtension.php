<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Type;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\DataCollector\EventListener\DataCollectorListener;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataCollectorTypeExtension extends AbstractDataTableTypeExtension
{
    private DataCollectorListener $listener;

    public function __construct(DataTableDataCollectorInterface $dataCollector)
    {
        $this->listener = new DataCollectorListener($dataCollector);
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->listener);
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}
