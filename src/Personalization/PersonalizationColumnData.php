<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnData
{
    private static ?OptionsResolver $optionsResolver = null;

    public function __construct(
        public string $name,
        public int $priority = 0,
        public bool $visible = true,
    ) {
    }

    /**
     * @param array{name: string, order: int, visible: bool} $data
     */
    public static function fromArray(array $data): self
    {
        $resolver = self::$optionsResolver ??= (new OptionsResolver())
            ->setRequired('name')
            ->setDefaults([
                'priority' => 0,
                'visible' => true,
            ])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('priority', 'int')
            ->setAllowedTypes('visible', 'bool')
        ;

        $data = $resolver->resolve($data);

        return new self(
            $data['name'],
            $data['priority'],
            $data['visible'],
        );
    }

    public static function fromColumn(ColumnInterface $column): self
    {
        return new self(
            $column->getName(),
            $column->getPriority(),
            $column->isVisible(),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        // TODO: Remove BC layer made for easier update to version 0.14
        if (isset($this->order)) {
            $this->priority = (int) $this->order;
        }

        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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
