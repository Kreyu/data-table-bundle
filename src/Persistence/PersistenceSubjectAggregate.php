<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

/**
 * Persistence subject that holds a reference to an original subject.
 */
class PersistenceSubjectAggregate implements PersistenceSubjectInterface
{
    public function __construct(
        private string $identifier,
        private mixed $subject,
    ) {
    }

    public function getDataTablePersistenceIdentifier(): string
    {
        return str_replace(['{','}','(',')','/','\\','@',':'], '_', $this->identifier);
    }

    public function getSubject(): mixed
    {
        return $this->subject;
    }
}
