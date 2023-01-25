<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Persistence\PersonalizationPersisterSubjectInterface;

class PersonalizationConfig
{
    public function __construct(
        private bool $enabled,
        private null|PersonalizationPersisterInterface $persister,
        private null|PersonalizationPersisterSubjectInterface $persisterSubject,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPersister(): ?PersonalizationPersisterInterface
    {
        return $this->persister;
    }

    public function hasPersister(): bool
    {
        return null !== $this->persister;
    }

    public function getPersisterSubject(): ?PersonalizationPersisterSubjectInterface
    {
        return $this->persisterSubject;
    }

    public function hasPersisterSubject(): bool
    {
        return null !== $this->persisterSubject;
    }
}