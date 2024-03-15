<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Persistence;

use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\StaticPersistenceSubjectProvider;
use PHPUnit\Framework\TestCase;

class StaticPersistenceSubjectProviderTest extends TestCase
{
    public function testGetDataTablePersistenceIdentifier(): void
    {
        $provider = new StaticPersistenceSubjectProvider('foo');

        $this->assertSame('foo', $provider->getDataTablePersistenceIdentifier());
    }

    public function testProvide(): void
    {
        $provider = new StaticPersistenceSubjectProvider('foo');

        $subject = $provider->provide();

        $this->assertSame($provider, $subject);
        $this->assertInstanceOf(PersistenceSubjectInterface::class, $subject);
    }
}
