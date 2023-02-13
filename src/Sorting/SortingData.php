<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortingData
{
    /**
     * @param array<SortingFieldData> $fields
     */
    public function __construct(
        private array $fields = [],
    ) {
        foreach ($fields as $field) {
            if (!$field instanceof SortingFieldData) {
                throw new UnexpectedTypeException($field, SortingFieldData::class);
            }
        }
    }

    public static function fromArray(array $data): static
    {
        ($resolver = new OptionsResolver())
            ->setDefault('fields', function (OptionsResolver $resolver) {
                $resolver
                    ->setPrototype(true)
                    ->setRequired([
                        'name',
                    ])
                    ->setDefaults([
                        'direction' => 'asc',
                    ])
                ;
            })
            ->setAllowedTypes('fields', ['array'])
        ;

        $data = $resolver->resolve($data);

        $fields = array_map(
            fn (array $field) => SortingFieldData::fromArray($field),
            $data['fields'],
        );

        return new static($fields);
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
