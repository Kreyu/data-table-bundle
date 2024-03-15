<?php

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures;

use Symfony\Component\Security\Core\User\UserInterface;

class SimpleUser implements UserInterface
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
        return 'john-doe';
    }
}
