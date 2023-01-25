<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface DataTableFactoryInterface
{
    public function create(string $type, ?ProxyQueryInterface $query = null, array $options = []): DataTableInterface;

    public function createNamed(string $name, string $type, ?ProxyQueryInterface $query = null, array $options = []): DataTableInterface;

    public function createBuilder(string $type, ?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface;

    public function createNamedBuilder(string $name, string $type, ?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface;
}
