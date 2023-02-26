<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

readonly class FilterData
{
    public function __construct(
        public mixed $value = '',
        public mixed $operator = null,
    ) {
    }

    public static function fromArray(array $data = []): self
    {
        ($resolver = new OptionsResolver())
            ->setRequired('value')
            ->setDefault('operator', null)
            ->setAllowedTypes('operator', ['null', 'string', Operator::class])
            ->setNormalizer('operator', function (Options $options, mixed $value): ?Operator {
                return is_string($value) ? Operator::from($value) : $value;
            })
        ;

        $data = $resolver->resolve($data);

        return new self(
            value: $data['value'],
            operator: $data['operator'],
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'operator' => $this->operator,
        ];
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
