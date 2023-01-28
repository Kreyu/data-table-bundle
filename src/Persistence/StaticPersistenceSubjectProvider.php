<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

class StaticPersistenceSubjectProvider implements PersistenceSubjectProviderInterface
{
    public function provide(): PersistenceSubjectInterface
    {
        return new class implements PersistenceSubjectInterface
        {
            public function getDataTablePersistenceIdentifier(): string
            {
                return 'static';
            }
        };
    }
}
