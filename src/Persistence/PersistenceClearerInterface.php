<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

interface PersistenceClearerInterface
{
    public function clear(PersistenceSubjectInterface $subject): void;
}
