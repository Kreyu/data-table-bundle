<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

class PaginationData
{
    public function __construct(
        private int $page,
        private int $perPage,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    public function getOffset(): int
    {
        return $this->perPage * ($this->page - 1);
    }
}
