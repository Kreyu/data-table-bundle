<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

class ResultSet implements ResultSetInterface
{
    public function __construct(
        protected readonly \Iterator $iterator,
        protected ?int $currentPageItemCount = null,
        protected ?int $totalItemCount = null,
    ) {
    }

    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }

    public function getCurrentPageItemCount(): ?int
    {
        return $this->currentPageItemCount;
    }

    public function getTotalItemCount(): ?int
    {
        return $this->totalItemCount ??= $this->currentPageItemCount;
    }

    public function count(): int
    {
        return $this->getCurrentPageItemCount() ?? $this->getTotalItemCount();
    }
}
