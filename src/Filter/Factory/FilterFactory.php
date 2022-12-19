<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Factory;

use Kreyu\Bundle\DataTableBundle\Filter\FilterChainInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private readonly FilterChainInterface $filterChain,
    ) {
    }

    /**
     * @param class-string<FilterInterface> $type
     */
    public function create(string $name, string $type, array $options = []): FilterInterface
    {
        if (null === $filter = $this->filterChain->get($type)) {
            throw new \InvalidArgumentException(sprintf('Could not load type "%s".', $type));
        }

        $filter = clone $filter;
        $filter->initialize($name, $options);

        return $filter;
    }
}
