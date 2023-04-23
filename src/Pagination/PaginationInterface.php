<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

interface PaginationInterface
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 25;

    public function getItems(): iterable;

    public function getCurrentPageNumber(): int;

    public function getCurrentPageItemCount(): int;

    public function getTotalItemCount(): int;

    public function getItemNumberPerPage(): ?int;

    public function getPageCount(): int;

    public function hasPreviousPage(): bool;

    public function hasNextPage(): bool;

    public function getFirstVisiblePageNumber(): int;

    public function getLastVisiblePageNumber(): int;

    public function getCurrentPageFirstItemIndex(): int;

    public function getCurrentPageLastItemIndex(): int;
}
