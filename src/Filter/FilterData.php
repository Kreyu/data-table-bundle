<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterData
{
    public function __construct(
        private mixed $value = null,
        private ?Operator $operator = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data = []): self
    {
        ($resolver = new OptionsResolver())
            ->setDefaults([
                'value' => '',
                'operator' => null,
            ])
            ->setAllowedTypes('operator', ['null', 'string', Operator::class])
            ->setNormalizer(
                'operator',
                static fn (Options $options, string|Operator|null $value): ?Operator => is_string($value)
                    ? Operator::from($value)
                    : $value,
            )
            ->setIgnoreUndefined()
        ;

        $data = $resolver->resolve($data);

        /** @var Operator|null $operator */
        $operator = $data['operator'];

        return new self(
            value: $data['value'],
            operator: $operator,
        );
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator(mixed $operator): void
    {
        if ($operator === null) {
            $this->operator = null;

            return;
        }

        if (!$operator instanceof Operator) {
            throw new UnexpectedTypeException($operator, Operator::class);
        }

        $this->operator = $operator;
    }

    public function hasValue(): bool
    {
        return null !== $this->value && '' !== $this->value && [] !== $this->value;
    }
}
