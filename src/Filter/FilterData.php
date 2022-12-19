<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

class FilterData
{
    public function __construct(
        private readonly null|Operator $operator,
        private readonly mixed         $value,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            operator: Operator::tryFrom($data['operator'] ?? ''),
            value: $data['value'] ?? '',
        );
    }

    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function hasValue(): bool
    {
        return '' !== $this->value;
    }
}