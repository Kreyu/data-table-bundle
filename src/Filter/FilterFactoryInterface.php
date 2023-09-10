<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

interface FilterFactoryInterface
{
    /**
     * @param class-string<FilterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function create(string $type = FilterType::class, array $options = []): FilterInterface;

    /**
     * @param class-string<FilterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamed(string $name, string $type = FilterType::class, array $options = []): FilterInterface;

    /**
     * @param class-string<FilterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createBuilder(string $type = FilterType::class, array $options = []): FilterBuilderInterface;

    /**
     * @param class-string<FilterTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamedBuilder(string $name, string $type = FilterType::class, array $options = []): FilterBuilderInterface;
}
