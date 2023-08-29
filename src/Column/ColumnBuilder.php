<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

class ColumnBuilder extends ColumnConfigBuilder implements ColumnBuilderInterface
{
    private int $priority = 0;
    private bool $visible = true;

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function getColumn(): ColumnInterface
    {
        return (new Column($this->getColumnConfig()))
            ->setPriority($this->getPriority())
            ->setVisible($this->isVisible())
        ;
    }
}
