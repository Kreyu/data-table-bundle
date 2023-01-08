<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use function Symfony\Component\String\u;

class CacheFilterPersister implements FilterPersisterInterface
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(FilterPersisterSubjectInterface $subject, DataTableInterface $dataTable): array
    {
        return $this->cache->get(
            $this->getCacheKey($subject, $dataTable),
            fn () => [],
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function save(FilterPersisterSubjectInterface $subject, DataTableInterface $dataTable, array $filters): void
    {
        $cacheKey = $this->getCacheKey($subject, $dataTable);

        // Normalize filters data, converting Operator enums to their (string) value.
        foreach ($filters as $index => $filter) {
            if ($filter['operator'] instanceof Operator) {
                $filters[$index]['operator'] = $filter['operator']->value;
            }
        }

        $this->cache->delete($cacheKey);
        $this->cache->get($cacheKey, fn () => $filters);
    }

    protected function getCacheKey(FilterPersisterSubjectInterface $subject, DataTableInterface $dataTable): string
    {
       return (string) u($dataTable->getName().'_filter_'.$subject->getFilterPersisterIdentifier())->snake();
    }
}
