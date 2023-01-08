<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Persistence;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenStoragePersonalizationPersisterSubjectProvider implements PersonalizationPersisterSubjectProviderInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function provide(): ?PersonalizationPersisterSubjectInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if ($user instanceof PersonalizationPersisterSubjectInterface) {
            return $user;
        }

        return null;
    }
}
