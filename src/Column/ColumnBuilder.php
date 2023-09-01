<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ColumnBuilder extends ColumnConfigBuilder implements ColumnBuilderInterface
{
    private int $priority = 0;
    private bool $visible = true;

    public function getPriority(): int
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->priority = $priority;

        return $this;
    }

    public function isVisible(): bool
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->visible = $visible;

        return $this;
    }

    public function getColumn(): ColumnInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return (new Column($this->getColumnConfig()))
            ->setPriority($this->getPriority())
            ->setVisible($this->isVisible())
        ;
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('ColumnBuilder methods cannot be accessed anymore once the builder is turned into a ColumnConfigInterface instance.');
    }
}
