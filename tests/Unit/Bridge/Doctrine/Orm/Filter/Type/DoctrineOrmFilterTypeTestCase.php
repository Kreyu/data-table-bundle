<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Test\Filter\FilterTypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class DoctrineOrmFilterTypeTestCase extends FilterTypeTestCase
{
    protected function getDefaultOperator(): Operator
    {
        return Operator::Equals;
    }

    abstract protected function getSupportedOperators(): array;

    protected function getDefaultFormType(): string
    {
        return TextType::class;
    }

    public function testItShouldUseDefaultOperator()
    {
        $this->assertEquals($this->getDefaultOperator(), $this->createFilter()->getConfig()->getDefaultOperator());
    }

    public function testItShouldSupportOperators()
    {
        $this->assertEquals($this->getSupportedOperators(), $this->createFilter()->getConfig()->getSupportedOperators());
    }
}
