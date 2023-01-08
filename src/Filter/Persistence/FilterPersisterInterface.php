<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;

interface FilterPersisterInterface
{
    public function get(FilterPersisterSubjectInterface $subject, DataTableInterface $dataTable): array;

    public function save(FilterPersisterSubjectInterface $subject, DataTableInterface $dataTable, array $filters): void;
}
