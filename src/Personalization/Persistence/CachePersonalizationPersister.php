<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use function Symfony\Component\String\u;

class CachePersonalizationPersister implements PersonalizationPersisterInterface
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(PersonalizationPersisterSubjectInterface $subject, DataTableInterface $dataTable): PersonalizationData
    {
        $personalizationData = new PersonalizationData($dataTable->getColumns());

        $personalizationFormData = $this->cache->get(
            $this->getCacheKey($subject, $dataTable),
            fn () => $personalizationData->toFormData(),
        );

        $personalizationData->fromFormData($personalizationFormData);

        return $personalizationData;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function save(PersonalizationPersisterSubjectInterface $subject, DataTableInterface $dataTable, PersonalizationData $personalizationData): void
    {
        $cacheKey = $this->getCacheKey($subject, $dataTable);

        $this->cache->delete($cacheKey);
        $this->cache->get($cacheKey, fn () => $personalizationData->toFormData());
    }

    protected function getCacheKey(PersonalizationPersisterSubjectInterface $subject, DataTableInterface $dataTable): string
    {
        return (string) u($dataTable->getName().'_personalization_'.$subject->getPersonalizationPersisterIdentifier())->snake();
    }
}
