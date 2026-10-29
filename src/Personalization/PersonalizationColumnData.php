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
    ) {}

    /**
     * @param array{name: string, priority?: int, visible?: bool} $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array{name: string, priority: int, visible: bool} $resolvedData */
        $resolvedData = self::getOptionsResolver()->resolve($data);

        return new self(
            $resolvedData['name'],
            $resolvedData['priority'],
            $resolvedData['visible'],
        );
    }

    public static function fromColumn(ColumnInterface $column): self
    {
        return new self(
            $column->getName(),
            $column->getConfig()->getPriority(),
            $column->getConfig()->isVisible(),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        $properties = get_object_vars($this);

        /** @var int|null $legacyOrder */
        $legacyOrder = $properties['order'] ?? null;

        if ($legacyOrder !== null) {
            $this->priority = $legacyOrder;
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

    private static function getOptionsResolver(): OptionsResolver
    {
        return self::$optionsResolver ??= (new OptionsResolver())
            ->setRequired('name')
            ->setDefaults([
                'priority' => 0,
                'visible' => true,
            ])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('priority', 'int')
            ->setAllowedTypes('visible', 'bool')
        ;
    }
}
