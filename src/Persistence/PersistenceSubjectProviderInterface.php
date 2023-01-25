<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

interface PersistenceSubjectProviderInterface
{
    /**
     * @throws PersistenceSubjectNotFoundException
     */
    public function provide(): PersistenceSubjectInterface;
}
