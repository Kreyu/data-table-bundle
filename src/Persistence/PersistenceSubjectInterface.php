<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

interface PersistenceSubjectInterface
{
    public function getDataTablePersistenceIdentifier();
}
