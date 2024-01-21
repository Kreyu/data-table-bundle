<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Event;

use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreSetParametersEvent;
use PHPUnit\Framework\TestCase;

class PreSetParametersEventTest extends TestCase
{
    public function testSetParameters()
    {
        $event = new PreSetParametersEvent(
            $this->createMock(ProxyQueryInterface::class),
            $this->createMock(FilterData::class),
            $this->createMock(FilterInterface::class),
            [],
        );

        $parameters = [new Parameter('foo', 'bar')];

        $event->setParameters($parameters);

        $this->assertEquals($parameters, $event->getParameters());
    }
}
