<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilder;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class ExporterBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetExporter(): void
    {
        $builder = $this->createBuilder();

        $action = $builder->getExporter();
        $config = $builder->getExporterConfig();

        $this->assertEquals($config, $action->getConfig());
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(): ExporterBuilder
    {
        return new ExporterBuilder(
            name: 'foo',
            type: $this->createStub(ResolvedExporterTypeInterface::class),
        );
    }
}
