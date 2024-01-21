<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Test\Filter\FilterTypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterTypeTest extends FilterTypeTestCase
{
    public function testNameAccessibleInViewVars(): void
    {
        $filter = $this->createNamedFilter('foo');
        $filterView = $this->createFilterView($filter);

        $this->assertEquals('foo', $filterView->vars['name']);
    }

    public function testLabelDefaultValueInheritsFromSentenceCasedFilterName(): void
    {
        $filter = $this->createNamedFilter('fooBar');
        $filterView = $this->createFilterView($filter);

        $this->assertNull($filter->getConfig()->getOption('label'));
        $this->assertEquals('Foo bar', $filterView->vars['label']);
    }

    public function testPassingLabelAsOption(): void
    {
        $filter = $this->createFilter(['label' => 'foo']);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals('foo', $filter->getConfig()->getOption('label'));
        $this->assertEquals('foo', $filterView->vars['label']);
    }

    public function testLabelTranslationParametersDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertEmpty($filter->getConfig()->getOption('label_translation_parameters'));
        $this->assertEmpty($filterView->vars['label_translation_parameters']);
    }

    public function testPassingLabelTranslationParametersAsOption(): void
    {
        $labelTranslationParameters = ['foo' => 'bar'];

        $filter = $this->createFilter(['label_translation_parameters' => $labelTranslationParameters]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals($labelTranslationParameters, $filter->getConfig()->getOption('label_translation_parameters'));
        $this->assertEquals($labelTranslationParameters, $filterView->vars['label_translation_parameters']);
    }

    public function testTranslationDomainDefaultValueInheritsFromDataTableTranslationDomain(): void
    {
        $dataTableView = $this->createDataTableViewMock();
        $dataTableView->vars['translation_domain'] = 'foo';

        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter, parent: $dataTableView);

        $this->assertNull($filter->getConfig()->getOption('translation_domain'));
        $this->assertEquals('foo', $filterView->vars['translation_domain']);
    }

    public function testPassingTranslationDomainAsOption(): void
    {
        $dataTableView = $this->createDataTableViewMock();
        $dataTableView->vars['translation_domain'] = 'bar';

        $filter = $this->createFilter(['translation_domain' => 'foo']);
        $filterView = $this->createFilterView($filter, parent: $dataTableView);

        // Additionally tests whether the given translation domain is not overwritten by the data table one

        $this->assertEquals('foo', $filter->getConfig()->getOption('translation_domain'));
        $this->assertEquals('foo', $filterView->vars['translation_domain']);
    }

    public function testQueryPathDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertNull($filter->getConfig()->getOption('query_path'));
        $this->assertEquals($filter->getQueryPath(), $filterView->vars['query_path']);
    }

    public function testPassingQueryPathAsOption(): void
    {
        $filter = $this->createFilter(['query_path' => 'foo']);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals('foo', $filter->getConfig()->getOption('query_path'));
        $this->assertEquals('foo', $filterView->vars['query_path']);
    }

    public function testFormTypeDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertEquals(TextType::class, $filter->getConfig()->getFormType());
        $this->assertEquals(TextType::class, $filterView->vars['form_type']);
    }

    public function testPassingFormTypeAsOption(): void
    {
        $filter = $this->createFilter(['form_type' => NumberType::class]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals(NumberType::class, $filter->getConfig()->getFormType());
        $this->assertEquals(NumberType::class, $filterView->vars['form_type']);
    }

    public function testFormOptionsDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertEmpty($filter->getConfig()->getFormOptions());
        $this->assertEmpty($filterView->vars['form_options']);
    }

    public function testPassingFormOptionsAsOption(): void
    {
        $formOptions = ['scale' => 2];

        $filter = $this->createFilter(['form_options' => $formOptions]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals($formOptions, $filter->getConfig()->getFormOptions());
        $this->assertEquals($formOptions, $filterView->vars['form_options']);
    }

    public function testOperatorFormTypeDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertEquals(OperatorType::class, $this->createFilter()->getConfig()->getOperatorFormType());
        $this->assertEquals(OperatorType::class, $filterView->vars['operator_form_type']);
    }

    public function testPassingOperatorFormTypeAsOption(): void
    {
        $filter = $this->createFilter(['operator_form_type' => NumberType::class]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals(NumberType::class, $filter->getConfig()->getOperatorFormType());
        $this->assertEquals(NumberType::class, $filterView->vars['operator_form_type']);
    }

    public function testOperatorFormOptionsDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertEmpty($filter->getConfig()->getOperatorFormOptions());
        $this->assertEmpty($filterView->vars['form_options']);
    }

    public function testPassingOperatorFormOptionsAsOption(): void
    {
        $operatorFormOptions = ['scale' => 2];

        $filter = $this->createFilter(['operator_form_options' => $operatorFormOptions]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals($operatorFormOptions, $filter->getConfig()->getOperatorFormOptions());
        $this->assertEquals($operatorFormOptions, $filterView->vars['operator_form_options']);
    }

    public function testDefaultOperatorDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertEquals(Operator::Equals, $filter->getConfig()->getDefaultOperator());
        $this->assertEquals(Operator::Equals, $filterView->vars['default_operator']);
    }

    public function testPassingDefaultOperatorAsOption(): void
    {
        $filter = $this->createFilter(['default_operator' => Operator::NotEquals]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals(Operator::NotEquals, $filter->getConfig()->getDefaultOperator());
        $this->assertEquals(Operator::NotEquals, $filterView->vars['default_operator']);
    }

    public function testSupportedOperatorsDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $defaultOperator = $filter->getConfig()->getDefaultOperator();

        $this->assertEquals([$defaultOperator], $filter->getConfig()->getSupportedOperators());
        $this->assertEquals([$defaultOperator], $filterView->vars['supported_operators']);
    }

    public function testPassingSupportedOperatorsAsOption(): void
    {
        $expectedSupportedOperators = [Operator::Equals, Operator::NotEquals];

        $filter = $this->createFilter(['supported_operators' => $expectedSupportedOperators]);
        $filterView = $this->createFilterView($filter);

        $this->assertEquals($expectedSupportedOperators, $filter->getConfig()->getSupportedOperators());
        $this->assertEquals($expectedSupportedOperators, $filterView->vars['supported_operators']);
    }

    public function testPassingSupportedOperatorsAsOptionInheritsDefaultOperator(): void
    {
        $filter = $this->createFilter([
            'default_operator' => Operator::Contains,
            'supported_operators' => [Operator::Equals, Operator::NotEquals],
        ]);

        $filterView = $this->createFilterView($filter);

        $expectedSupportedOperators = [Operator::Equals, Operator::NotEquals, Operator::Contains];

        $this->assertEquals($expectedSupportedOperators, $filter->getConfig()->getSupportedOperators());
        $this->assertEquals($expectedSupportedOperators, $filterView->vars['supported_operators']);
    }

    public function testOperatorSelectableDefaultValue(): void
    {
        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter);

        $this->assertFalse($filter->getConfig()->isOperatorSelectable());
        $this->assertFalse($filterView->vars['operator_selectable']);
    }

    public function testPassingOperatorSelectableAsOption(): void
    {
        $filter = $this->createFilter(['operator_selectable' => true]);
        $filterView = $this->createFilterView($filter);

        $this->assertTrue($filter->getConfig()->isOperatorSelectable());
        $this->assertTrue($filterView->vars['operator_selectable']);
    }

    public function testDataAccessibleInViewVars(): void
    {
        $data = new FilterData('foo');

        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter, $data);

        $this->assertEquals($data, $filterView->vars['data']);
    }

    public function testValueAccessibleInViewVarsWithoutActiveFilterFormatter(): void
    {
        $data = new FilterData('foo');

        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter, $data);

        $this->assertEquals('foo', $filterView->vars['value']);
    }

    public function testPassingActiveFilterFormatterAsOption(): void
    {
        $formatter = static function (FilterData $data, FilterInterface $filter, array $options) {
            return sprintf('%s_%s_%d', $data->getValue(), $filter->getName(), count($options));
        };

        $filter = $this->createFilter([
            'active_filter_formatter' => $formatter,
        ]);

        $data = new FilterData('foo');

        $filterView = $this->createFilterView($filter, $data);

        $expectedValue = $formatter($data, $filter, $filter->getConfig()->getOptions());

        $this->assertEquals($expectedValue, $filterView->vars['value']);

        // Additionally tests whether the data is unmodified when formatter is present
        $this->assertEquals($data, $filterView->vars['data']);
    }

    public function testDataAndValueAccessibleInViewVarsWithoutActiveFilterFormatter(): void
    {
        $data = new FilterData('foo');

        $filter = $this->createFilter();
        $filterView = $this->createFilterView($filter, $data);

        $this->assertEquals($data, $filterView->vars['data']);
        $this->assertEquals('foo', $filterView->vars['value']);
    }

    protected function getTestedType(): string
    {
        return FilterType::class;
    }
}
