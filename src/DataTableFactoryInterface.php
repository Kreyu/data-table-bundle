<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

interface DataTableFactoryInterface
{
    public function create(string $type, mixed $data = null, array $options = []): DataTableInterface;

    public function createNamed(string $name, string $type, mixed $data = null, array $options = []): DataTableInterface;

    public function createBuilder(string $type, mixed $data = null, array $options = []): DataTableBuilderInterface;

    public function createNamedBuilder(string $name, string $type, mixed $data = null, array $options = []): DataTableBuilderInterface;
}
