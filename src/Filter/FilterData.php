<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterData
{
    public function __construct(
        private null|Operator $operator,
        private mixed $value,
    ) {
    }

    public static function fromArray(array $data = []): self
    {
        ($resolver = new OptionsResolver())
            ->setDefaults([
                'operator' => null,
                'value' => null,
            ])
            ->setAllowedTypes('operator', ['null', 'string', Operator::class])
            ->setNormalizer('operator', function (Options $options, mixed $value): ?Operator {
                if (null === $value) {
                    return null;
                }

                if ($value instanceof Operator) {
                    return $value;
                }

                return Operator::tryFrom((string) $value);
            })
        ;

        $data = $resolver->resolve($data);

        return new self(
            operator: $data['operator'],
            value: $data['value'],
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
