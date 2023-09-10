<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

class StaticPersistenceSubjectProvider implements PersistenceSubjectProviderInterface, PersistenceSubjectInterface
{
    public function __construct(
        private string $identifier = 'static',
    ) {
    }

    public function provide(): PersistenceSubjectInterface
    {
        return $this;
    }

    public function getDataTablePersistenceIdentifier(): string
    {
        return $this->identifier;
    }
}
