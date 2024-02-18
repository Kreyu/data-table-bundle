<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Action;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilder;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class ActionBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetAction(): void
    {
        $builder = $this->createBuilder();

        $action = $builder->getAction();
        $config = $builder->getActionConfig();

        $this->assertEquals($config, $action->getConfig());
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(): ActionBuilder
    {
        return new ActionBuilder(
            name: 'foo',
            type: $this->createStub(ResolvedActionTypeInterface::class),
        );
    }
}
