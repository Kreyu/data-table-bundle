<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

class Pagination implements PaginationInterface
{
    public function __construct(
        private readonly iterable $items,
        private readonly int $currentPageNumber,
        private readonly int $totalItemCount,
        private readonly int $itemNumberPerPage,
    ) {
    }

    public function getItems(): iterable
    {
        return $this->items;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    public function getItemNumberPerPage(): int
    {
        return $this->itemNumberPerPage;
    }

    public function getPageCount(): int
    {
        if ($this->itemNumberPerPage < 1) {
            return 1;
        }

        return (int) ceil($this->totalItemCount / $this->itemNumberPerPage);
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPageNumber > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPageNumber < $this->getPageCount();
    }
}
