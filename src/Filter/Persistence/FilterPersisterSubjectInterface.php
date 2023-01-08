<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Persistence;

interface FilterPersisterSubjectInterface
{
    public function getFilterPersisterIdentifier(): string;
}
