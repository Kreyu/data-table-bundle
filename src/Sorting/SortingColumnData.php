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
        private string $name,
        private string $direction = 'asc',
        string|PropertyPathInterface|null $propertyPath = null,
    ) {
        $this->propertyPath = $this->createPropertyPath($propertyPath);
    }

    /**
     * @param array{
     *     name: string,
     *     direction: "asc"|"desc"|"ASC"|"DESC",
     *     property_path: null|string|PropertyPathInterface
     * } $data
     */
    public static function fromArray(array $data): self
    {
        ($resolver = new OptionsResolver())
            ->setRequired('name')
            ->setDefault('direction', 'asc')
            ->setDefault('property_path', null)
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('property_path', ['null', 'string', PropertyPathInterface::class])
            ->setAllowedValues('direction', ['asc', 'desc', 'ASC', 'DESC'])
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

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param "asc"|"desc"|"ASC"|"DESC" $direction
     */
    public function withDirection(string $direction): self
    {
        $clone = clone $this;
        $clone->direction = $direction;

        return $clone;
    }

    public function getPropertyPath(): PropertyPathInterface
    {
        if (null === $this->propertyPath) {
            $this->propertyPath = new PropertyPath($this->name);
        }

        return $this->propertyPath;
    }

    public function withPropertyPath(string|PropertyPathInterface|null $propertyPath): self
    {
        $clone = clone $this;
        $clone->propertyPath = $this->createPropertyPath($propertyPath);

        return $clone;
    }

    private function createPropertyPath(string|PropertyPathInterface|null $propertyPath): ?PropertyPathInterface
    {
        if (is_string($propertyPath)) {
            $propertyPath = new PropertyPath($propertyPath);
        }

        return $propertyPath;
    }
}
