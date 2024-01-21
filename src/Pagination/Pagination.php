<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

class Pagination implements PaginationInterface
{
    public const SIDE_PAGE_LIMIT = 3;

    /**
     * @throws CurrentPageOutOfRangeException
     */
    public function __construct(
        private readonly int $currentPageNumber,
        private readonly int $currentPageItemCount,
        private readonly int $totalItemCount,
        private readonly ?int $itemNumberPerPage = null,
    ) {
        if ($totalItemCount > 0 && $this->isCurrentPageNumberOutOfRange()) {
            throw new CurrentPageOutOfRangeException();
        }
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    public function getCurrentPageItemCount(): int
    {
        return $this->currentPageItemCount;
    }

    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    public function getItemNumberPerPage(): ?int
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

    public function getFirstVisiblePageNumber(): int
    {
        $leftSideAddition = max(self::SIDE_PAGE_LIMIT - ($this->getPageCount() - $this->getCurrentPageNumber()), 0);

        return max($this->getCurrentPageNumber() - self::SIDE_PAGE_LIMIT - $leftSideAddition, 1);
    }

    public function getLastVisiblePageNumber(): int
    {
        return min($this->getFirstVisiblePageNumber() + (self::SIDE_PAGE_LIMIT * 2), $this->getPageCount());
    }

    public function getCurrentPageFirstItemIndex(): int
    {
        return $this->itemNumberPerPage * ($this->currentPageNumber - 1) + 1;
    }

    public function getCurrentPageLastItemIndex(): int
    {
        return $this->getCurrentPageFirstItemIndex() + $this->getCurrentPageItemCount() - 1;
    }

    private function isCurrentPageNumberOutOfRange(): bool
    {
        return $this->currentPageNumber < 1
            || $this->currentPageNumber > $this->getPageCount();
    }
}
