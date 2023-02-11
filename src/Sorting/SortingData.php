<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SortingData
{
    /**
     * @var array<SortingField>
     */
    private array $fields = [];

    /**
     * @param array<SortingField> $fields
     */
    public function __construct(
        array $fields = [],
    ) {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public static function fromArray(array $data): self
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

        return new self(
            fields: array_map(
                fn (array $field) => new SortingField($field['name'], $field['direction']),
                $data['fields'],
            ),
        );
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function hasField(string|SortingField $field): bool
    {
        if ($field instanceof SortingField) {
            $field = $field->getName();
        }

        return array_key_exists($field, $this->fields);
    }

    public function addField(SortingField $field): void
    {
        $this->fields[$field->getName()] = $field;
    }

    public function removeField(string|SortingField $field): void
    {
        if ($field instanceof SortingField) {
            $field = $field->getName();
        }

        unset($this->fields[$field]);
    }
}
