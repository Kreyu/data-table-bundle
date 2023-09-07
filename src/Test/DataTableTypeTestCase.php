<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

abstract class DataTableTypeTestCase extends DataTableIntegrationTestCase
{
    protected function createQueryMock(): ProxyQueryInterface
    {
        return $this->createMock(ProxyQueryInterface::class);
    }

    protected function createDataTable(array $options = []): DataTableInterface
    {
        return $this->factory->create($this->getTestedType(), $this->createQueryMock(), $options);
    }

    protected function createNamedDataTable(string $name, array $options = []): DataTableInterface
    {
        return $this->factory->createNamed($name, $this->getTestedType(), $this->createQueryMock(), $options);
    }
}
