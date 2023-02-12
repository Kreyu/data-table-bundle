<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnData
{
    private function __construct(
        private string $name,
        private int $order,
        private bool $visible,
    ) {
    }

    public static function fromArray(array $data): static
    {
        ($resolver = new OptionsResolver())
            ->setRequired('name')
            ->setDefaults([
                'order' => 0,
                'visible' => true,
            ])
            ->setAllowedTypes('name', 'string')
            ->setNormalizer('order', function (Options $options, mixed $value) {
                if (null === $value) {
                    return null;
                }

                return (int) $value;
            })
            ->setNormalizer('visible', function (Options $options, mixed $value) {
                return (bool) $value;
            })
        ;

        $data = $resolver->resolve($data);

        return new static(
            name: $data['name'],
            order: $data['order'],
            visible: $data['visible'],
        );
    }

    public static function fromColumn(ColumnInterface $column, int $order, bool $visible = true): static
    {
        return new static(
            name: $column->getName(),
            order: $order,
            visible: $visible,
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
}
