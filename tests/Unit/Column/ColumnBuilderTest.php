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
        $builder->setPriority(100);
        $builder->setVisible(false);

        $config = $builder->getColumnConfig();
        $column = $builder->getColumn();

        $this->assertEquals($config, $column->getConfig());
        $this->assertSame(100, $column->getPriority());
        $this->assertFalse($column->isVisible());
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    public function testGetPriority()
    {
        $builder = $this->createBuilder();

        $this->assertEquals(0, $builder->getPriority());
        $this->assertEquals(100, $builder->setPriority(100)->getPriority());
    }

    public function testIsVisible()
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->setVisible(true)->isVisible());
        $this->assertFalse($builder->setVisible(false)->isVisible());
    }

    private function createBuilder(): ColumnBuilder
    {
        return new ColumnBuilder(
            name: 'foo',
            type: $this->createStub(ResolvedColumnTypeInterface::class),
        );
    }
}
