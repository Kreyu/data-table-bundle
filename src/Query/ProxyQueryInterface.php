<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;

interface ProxyQueryInterface
{
    public function sort(string $field, string $direction): void;

    public function paginate(int $page, int $perPage): void;

    public function getPagination(): PaginationInterface;
}
