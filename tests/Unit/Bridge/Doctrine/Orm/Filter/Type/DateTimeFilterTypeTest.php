<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateTimeFilterType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DateTimeFilterTypeTest extends DoctrineOrmFilterTypeTestCase
{
    protected function getTestedType(): string
    {
        return DateTimeFilterType::class;
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::Equals,
            Operator::NotEquals,
            Operator::GreaterThan,
            Operator::GreaterThanEquals,
            Operator::LessThan,
            Operator::LessThanEquals,
        ];
    }

    protected function getDefaultFormType(): string
    {
        return DateTimeType::class;
    }

    public function testItShouldAddWidgetFormOptionWhenFormTypeIsDateType(): void
    {
        $formOptions = ['trim' => false];

        $filter = $this->createFilter(['form_options' => $formOptions]);

        $expectedFormOptions = $formOptions + ['widget' => 'single_text'];

        $this->assertEquals($expectedFormOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotOverwriteWidgetFormOptionIfGivenWhenFormTypeIsDateType(): void
    {
        $formOptions = ['widget' => 'choice'];

        $filter = $this->createFilter(['form_options' => $formOptions]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotModifyFormOptionsWhenFormTypeIsNotDateType(): void
    {
        $formOptions = ['trim' => false];

        $filter = $this->createFilter(['form_type' => TextType::class, 'form_options' => $formOptions]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }
}
