<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

interface ColumnBuilderInterface extends ColumnConfigBuilderInterface
{
    public function getPriority(): int;

    public function setPriority(int $priority): static;

    public function isVisible(): bool;

    public function setVisible(bool $visible): static;

    public function getColumn(): ColumnInterface;
}
