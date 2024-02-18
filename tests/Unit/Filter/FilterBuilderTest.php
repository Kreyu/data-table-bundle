<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilder;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetFilter(): void
    {
        $builder = $this->createBuilder();

        $action = $builder->getFilter();
        $config = $builder->getFilterConfig();

        $this->assertEquals($config, $action->getConfig());
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(): FilterBuilder
    {
        return new FilterBuilder(
            name: 'foo',
            type: $this->createStub(ResolvedFilterTypeInterface::class),
            dispatcher: $this->createStub(EventDispatcherInterface::class),
        );
    }
}
