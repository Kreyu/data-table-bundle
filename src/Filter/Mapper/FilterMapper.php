<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Mapper;

use Kreyu\Bundle\DataTableBundle\Filter\Factory\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class FilterMapper implements FilterMapperInterface
{
    private array $filters = [];

    public function __construct(
        private readonly FilterFactoryInterface $filterFactory,
    ) {
    }

    public function add(string $name, ?string $type = null, array $options = []): static
    {
        $this->filters[$name] = $this->filterFactory->create($name, $type, $options);

        return $this;
    }

    public function get(string $name): ?FilterInterface
    {
        return $this->filters[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->filters);
    }

    public function remove(string $name): FilterMapperInterface
    {
        unset($this->filters[$name]);

        return $this;
    }

    public function all(): array
    {
        return $this->filters;
    }
}
