<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Persistence;

interface PersonalizationPersisterSubjectProviderInterface
{
    public function provide(): ?PersonalizationPersisterSubjectInterface;
}
