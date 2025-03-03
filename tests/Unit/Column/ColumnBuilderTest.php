<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilder;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class ColumnBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetColumn(): void
    {
        $builder = $this->createBuilder();

        $config = $builder->getColumnConfig();
        $column = $builder->getColumn();

        $this->assertEquals($config, $column->getConfig());
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(): ColumnBuilder
    {
        return new ColumnBuilder(
            name: 'foo',
            type: $this->createStub(ResolvedColumnTypeInterface::class),
        );
    }
}
