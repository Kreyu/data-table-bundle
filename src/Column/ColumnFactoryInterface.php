<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

interface ColumnFactoryInterface
{
    /**
     * @param class-string<ColumnTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function create(string $type = ColumnType::class, array $options = []): ColumnInterface;

    /**
     * @param class-string<ColumnTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamed(string $name, string $type = ColumnType::class, array $options = []): ColumnInterface;

    /**
     * @param class-string<ColumnTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createBuilder(string $type = ColumnType::class, array $options = []): ColumnBuilderInterface;

    /**
     * @param class-string<ColumnTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamedBuilder(string $name, string $type = ColumnType::class, array $options = []): ColumnBuilderInterface;
}
