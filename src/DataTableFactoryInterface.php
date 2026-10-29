<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

interface DataTableFactoryInterface
{
    /**
     * @param class-string<DataTableTypeInterface> $type
     * @param array<string, mixed>                 $options
     */
    public function create(string $type, mixed $data = null, array $options = []): DataTableInterface;

    /**
     * @param class-string<DataTableTypeInterface> $type
     * @param array<string, mixed>                 $options
     */
    public function createNamed(
        string $name,
        string $type,
        mixed $data = null,
        array $options = [],
    ): DataTableInterface;

    /**
     * @param class-string<DataTableTypeInterface> $type
     * @param array<string, mixed>                 $options
     */
    public function createBuilder(string $type, mixed $data = null, array $options = []): DataTableBuilderInterface;

    /**
     * @param class-string<DataTableTypeInterface> $type
     * @param array<string, mixed>                 $options
     */
    public function createNamedBuilder(
        string $name,
        string $type,
        mixed $data = null,
        array $options = [],
    ): DataTableBuilderInterface;
}
