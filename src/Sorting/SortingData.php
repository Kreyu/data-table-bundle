<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

readonly class SortingData
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
        $fields = [];

        foreach ($data as $key => $value) {
            if ($value instanceof SortingFieldData) {
                $fields[$value->getName()] = $value;
            } elseif (is_array($value)) {
                $fields[$key] = SortingFieldData::fromArray($value);
            } elseif (is_string($key) && is_string($value)) {
                $fields[$key] = SortingFieldData::fromArray([
                    'name' => $key,
                    'direction' => $value,
                ]);
            }
        }

        return new static($fields);
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
