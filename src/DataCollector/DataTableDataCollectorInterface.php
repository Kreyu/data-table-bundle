<?php
declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\VarDumper\Cloner\Data;

interface DataTableDataCollectorInterface extends DataCollectorInterface
{
    public function collectFilter(DataTableInterface $dataTable, FiltrationData $filtrationData): void;

    public function getData(): array|Data;
}
