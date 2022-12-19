<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

interface PaginationInterface
{
    public function getItems(): iterable;

    public function getCurrentPageNumber(): int;

    public function getTotalItemCount(): int;

    public function getItemNumberPerPage(): int;

    public function getPageCount(): int;

    public function hasPreviousPage(): bool;

    public function hasNextPage(): bool;
}
