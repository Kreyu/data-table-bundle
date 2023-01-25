<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;

interface PersistenceAdapterInterface
{
    public function read(DataTableInterface $dataTable, PersistenceSubjectInterface $subject): mixed;

    public function write(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $data): void;
}
