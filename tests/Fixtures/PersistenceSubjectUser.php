<?php

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures;

use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PersistenceSubjectUser implements UserInterface, PersistenceSubjectInterface
{
    public function getRoles(): array
    {
        return ['foo', 'bar'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return 'jane-doe';
    }

    public function getDataTablePersistenceIdentifier(): string
    {
        return 'jane-doe-but-different';
    }
}
