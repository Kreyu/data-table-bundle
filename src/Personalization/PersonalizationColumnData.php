<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

class PersonalizationColumnData
{
    public function __construct(
        private string $name,
        private int $order,
        private bool $visible,
    ) {
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
