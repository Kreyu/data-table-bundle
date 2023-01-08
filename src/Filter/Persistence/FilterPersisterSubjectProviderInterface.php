<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Persistence;

interface FilterPersisterSubjectProviderInterface
{
    public function provide(): ?FilterPersisterSubjectInterface;
}
