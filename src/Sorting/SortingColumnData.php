<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class SortingColumnData
{
    private ?PropertyPathInterface $propertyPath;

    public function __construct(
        private readonly string $name,
        private string $direction = 'asc',
        null|string|PropertyPathInterface $propertyPath = null,
    ) {
        $this->setPropertyPath($propertyPath);
    }

    /**
     * @param array{name: string, direction: string, property_path: ?string} $data
     */
    public static function fromArray(array $data): self
    {
        ($resolver = new OptionsResolver())
            ->setRequired('name')
            ->setDefault('direction', 'asc')
            ->setDefault('property_path', null)
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('property_path', ['null', 'string'])
            ->setAllowedValues('direction', ['asc', 'desc'])
            ->addNormalizer('direction', function (Options $options, mixed $value) {
                return strtolower((string) $value);
            })
        ;

        $data = $resolver->resolve($data);

        return new self($data['name'], $data['direction'], $data['property_path']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }

    public function getPropertyPath(): PropertyPathInterface
    {
        if (null === $this->propertyPath) {
            $this->propertyPath = new PropertyPath($this->name);
        }

        return $this->propertyPath;
    }

    public function setPropertyPath(null|string|PropertyPathInterface $propertyPath): void
    {
        if (is_string($propertyPath)) {
            $propertyPath = new PropertyPath($propertyPath);
        }

        $this->propertyPath = $propertyPath;
    }
}
