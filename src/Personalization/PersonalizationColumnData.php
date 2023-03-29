<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnData
{
    public string $name;
    public int $order;
    public bool $visible;

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

        $self = new static();
        $self->name = $data['name'];
        $self->order = $data['order'];
        $self->visible = $data['visible'];

        return $self;
    }

    public static function fromColumn(ColumnInterface $column, int $order, bool $visible = true): static
    {
        $self = new static();
        $self->name = $column->getName();
        $self->order = $order;
        $self->visible = $visible;

        return $self;
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
