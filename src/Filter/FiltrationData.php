<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationData
{
    public function __construct(
        private array $filters = [],
    ) {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterData) {
                throw new UnexpectedTypeException($filter, FilterData::class);
            }
        }
    }

    public static function fromArray(array $data): static
    {
        ($resolver = new OptionsResolver())
            ->setDefault('filters', function (OptionsResolver $resolver) {
                $resolver
                    ->setPrototype(true)
                    ->setRequired([
                        'value',
                    ])
                    ->setDefaults([
                        'operator' => null,
                    ])
                ;
            })
            ->setAllowedTypes('filters', 'array[]')
        ;

        $data = $resolver->resolve($data);

        $filters = array_map(
            fn (array $filter) => FilterData::fromArray($filter),
            $data['filters'],
        );

        return new static($filters);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFilter(string|FilterInterface $filter): ?FilterData
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getFormName();
        }

        return $this->filters[$filter] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->filters);
    }

    public function hasActiveFilters(): bool
    {
        return !empty(array_filter($this->filters, fn (FilterData $filter) => $filter->hasValue()));
    }
}
