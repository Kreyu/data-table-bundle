<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortingColumnData
{
    public function __construct(
        private string $name,
        private string $direction = 'asc',
    ) {
    }

    public static function fromArray(array $data): self
    {
        ($resolver = new OptionsResolver())
            ->setRequired('name')
            ->setDefault('direction', 'asc')
            ->setAllowedTypes('name', 'string')
            ->setAllowedValues('direction', [null, 'asc', 'desc'])
            ->setNormalizer('direction', function (Options $options, mixed $value) {
                if (null === $value) {
                    return 'asc';
                }

                return strtolower((string) $value);
            })
        ;

        $data = $resolver->resolve($data);

        return new self($data['name'], $data['direction']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
