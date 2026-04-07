<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\RowGroup;

use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationDefinition;
use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationType;
use Kreyu\Bundle\DataTableBundle\RowGroup\RowGroupingConfiguration;
use PHPUnit\Framework\TestCase;

class RowGroupingConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $config = new RowGroupingConfiguration();

        $this->assertSame([], $config->getFields());
        $this->assertTrue($config->isEnabled());
        $this->assertNull($config->getStartRenderer());
        $this->assertNull($config->getEndRenderer());
        $this->assertSame([], $config->getAggregations());
        $this->assertTrue($config->hasStartRenderer());
        $this->assertFalse($config->hasEndRenderer());
    }

    public function testSetFields(): void
    {
        $config = new RowGroupingConfiguration();
        $config->setFields(['office', 'department']);

        $this->assertSame(['office', 'department'], $config->getFields());
    }

    public function testSetEnabled(): void
    {
        $config = new RowGroupingConfiguration();
        $config->setEnabled(false);

        $this->assertFalse($config->isEnabled());
    }

    public function testSetStartRenderer(): void
    {
        $renderer = fn () => [];
        $config = new RowGroupingConfiguration();
        $config->setStartRenderer($renderer);

        $this->assertNotNull($config->getStartRenderer());
        $this->assertTrue($config->hasStartRenderer());
    }

    public function testSetEndRenderer(): void
    {
        $renderer = fn () => [];
        $config = new RowGroupingConfiguration();
        $config->setEndRenderer($renderer);

        $this->assertNotNull($config->getEndRenderer());
        $this->assertTrue($config->hasEndRenderer());
    }

    public function testAggregations(): void
    {
        $config = new RowGroupingConfiguration();

        $agg1 = new AggregationDefinition('salary', AggregationType::Sum);
        $agg2 = new AggregationDefinition('salary', AggregationType::Count);

        $config->addAggregation($agg1);
        $config->addAggregation($agg2);

        $this->assertCount(2, $config->getAggregations());
        $this->assertSame($agg1, $config->getAggregations()[0]);
        $this->assertSame($agg2, $config->getAggregations()[1]);
    }

    public function testSetAggregationsReplacesAll(): void
    {
        $config = new RowGroupingConfiguration();
        $config->addAggregation(new AggregationDefinition('salary', AggregationType::Sum));

        $newAgg = new AggregationDefinition('bonus', AggregationType::Average);
        $config->setAggregations([$newAgg]);

        $this->assertCount(1, $config->getAggregations());
        $this->assertSame($newAgg, $config->getAggregations()[0]);
    }

    public function testFluentInterface(): void
    {
        $config = new RowGroupingConfiguration();

        $result = $config
            ->setFields(['office'])
            ->setEnabled(true)
            ->setStartRenderer(fn () => [])
            ->setEndRenderer(fn () => [])
            ->addAggregation(new AggregationDefinition('salary', AggregationType::Sum));

        $this->assertSame($config, $result);
    }

    public function testConstructorWithArguments(): void
    {
        $config = new RowGroupingConfiguration(
            fields: ['office'],
            enabled: false,
            aggregations: [new AggregationDefinition('salary', AggregationType::Sum)],
        );

        $this->assertSame(['office'], $config->getFields());
        $this->assertFalse($config->isEnabled());
        $this->assertCount(1, $config->getAggregations());
    }
}
