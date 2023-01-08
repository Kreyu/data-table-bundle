<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

class PersonalizationColumn
{
    public function __construct(
        private readonly ColumnInterface $column,
        private int $order,
        private bool $visible,
    ) {
    }

    public function getColumn(): ColumnInterface
    {
        return $this->column;
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
