<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Extension;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Extension\DoctrineOrmFilterExtension;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\BooleanFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateTimeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use PHPUnit\Framework\TestCase;

class DoctrineOrmFilterExtensionTest extends TestCase
{
    public function testItLoadsTypes(): void
    {
        $extension = new DoctrineOrmFilterExtension();

        $types = [
            DoctrineOrmFilterType::class,
            StringFilterType::class,
            NumericFilterType::class,
            BooleanFilterType::class,
            DateFilterType::class,
            DateTimeFilterType::class,
            DateRangeFilterType::class,
            EntityFilterType::class,
        ];

        foreach ($types as $type) {
            $this->assertTrue($extension->hasType($type));
            $this->assertInstanceOf($type, $extension->getType($type));
        }
    }
}
