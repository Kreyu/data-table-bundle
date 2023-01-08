<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Persistence;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenStorageFilterPersisterSubjectProvider implements FilterPersisterSubjectProviderInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function provide(): ?FilterPersisterSubjectInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if ($user instanceof FilterPersisterSubjectInterface) {
            return $user;
        }

        return null;
    }
}
