<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationData
{
    public function __construct(
        private array $filters = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        ($resolver = new OptionsResolver)
            ->setDefault('filters', function (OptionsResolver $resolver) {
                $resolver
                    ->setPrototype(true)
                    ->setRequired([
                        'operator',
                        'value',
                    ])
                ;
            })
            ->setAllowedTypes('filters', ['array'])
        ;

        $data = $resolver->resolve($data);

        return new self(
            filters: $data['filters'],
        );
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function hasFilter(FilterInterface $filter): bool
    {
        return array_key_exists($filter->getFormName(), $this->filters);
    }

    public function getFilterData(FilterInterface $filter): ?FilterData
    {
        if (!$this->hasFilter($filter)) {
            return null;
        }

        return FilterData::fromArray($this->filters[$filter->getFormName()]);
    }
}
