<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

class SortingData
{
    private array $fields = [];

    public function __construct(
        array $fields = [],
    ) {
        foreach ($fields as $field) {
            $this->addField($field);
        }
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
