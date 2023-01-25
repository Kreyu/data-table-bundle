<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
            return $user;
        }

        throw PersistenceSubjectNotFoundException::createForProvider($this);
    }
}
