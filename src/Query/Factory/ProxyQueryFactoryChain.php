<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query\Factory;

class ProxyQueryFactoryChain implements ProxyQueryFactoryChainInterface
{
    /**
     * @param iterable<ProxyQueryFactoryInterface> $factories
     */
    public function __construct(
        private readonly iterable $factories,
    ) {
    }

    public function getDataCompatibleFactory(mixed $data): ProxyQueryFactoryInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($data)) {
                return $factory;
            }
        }

        throw new \InvalidArgumentException('There are no proxy query factories supporting given data type!');
    }
}
