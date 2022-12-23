<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Factory;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

interface DataTableFactoryInterface
{
    /**
     * @param class-string<DataTableTypeInterface> $typeClass
     */
    public function create(string $typeClass): DataTableInterface;

    /**
     * @param class-string<DataTableTypeInterface> $typeClass
     */
    public function createNamed(string $name, string $typeClass): DataTableInterface;
}
