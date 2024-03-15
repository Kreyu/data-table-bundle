<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Persistence;

use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectAggregate;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectNotFoundException;
use Kreyu\Bundle\DataTableBundle\Persistence\TokenStoragePersistenceSubjectProvider;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\PersistenceSubjectUser;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\SimpleUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenStoragePersistenceSubjectProviderTest extends TestCase
{
    public function testProvideWithSimpleUser()
    {
        $subject = $this->createProvider($user = new SimpleUser())->provide();

        $this->assertInstanceOf(PersistenceSubjectAggregate::class, $subject);
        $this->assertSame($user, $subject->getSubject());
        $this->assertSame('john-doe', $subject->getDataTablePersistenceIdentifier());
    }

    public function testProvideWithPersistenceSubjectUser()
    {
        $subject = $this->createProvider($user = new PersistenceSubjectUser())->provide();

        $this->assertInstanceOf(PersistenceSubjectAggregate::class, $subject);
        $this->assertSame($user, $subject->getSubject());
        $this->assertSame('jane-doe-but-different', $subject->getDataTablePersistenceIdentifier());
    }

    public function testProvideWithNoUser()
    {
        $this->expectException(PersistenceSubjectNotFoundException::class);

        $this->createProvider()->provide()->getDataTablePersistenceIdentifier();
    }

    private function createProvider(?UserInterface $user = null): TokenStoragePersistenceSubjectProvider
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        if (null !== $user) {
            $token = $this->createMock(TokenInterface::class);
            $token->method('getUser')->willReturn($user);

            $tokenStorage->method('getToken')->willReturn($token);
        }

        return new TokenStoragePersistenceSubjectProvider($tokenStorage);
    }
}
