<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenStoragePersistenceSubjectProvider implements PersistenceSubjectProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function provide(): PersistenceSubjectInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if ($user instanceof PersistenceSubjectInterface) {
            return new PersistenceSubjectAggregate(
                $user->getDataTablePersistenceIdentifier(),
                $user,
            );
        }

        if ($user instanceof UserInterface) {
            return new PersistenceSubjectAggregate(
                $user->getUserIdentifier(),
                $user,
            );
        }

        throw PersistenceSubjectNotFoundException::createForProvider($this);
    }
}
