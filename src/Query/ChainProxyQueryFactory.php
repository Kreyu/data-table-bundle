<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class ChainProxyQueryFactory implements ProxyQueryFactoryInterface
{
    /**
     * @param array<ProxyQueryFactoryInterface> $factories
     */
    public function __construct(
        private readonly iterable $factories,
    ) {
    }

    public function create(mixed $data): ProxyQueryInterface
    {
        foreach ($this->factories as $factory) {
            try {
                return $factory->create($data);
            } catch (UnexpectedTypeException) {
            }
        }

        throw new InvalidArgumentException(sprintf('Unable to create proxy query for data of type "%s"', get_debug_type($data)));
    }
}
