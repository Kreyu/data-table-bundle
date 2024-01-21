<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\BooleanFilterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Translation\TranslatableMessage;

class BooleanFilterTypeTest extends DoctrineOrmFilterTypeTestCase
{
    protected function getTestedType(): string
    {
        return BooleanFilterType::class;
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::Equals,
            Operator::NotEquals,
        ];
    }

    protected function getDefaultFormType(): string
    {
        return ChoiceType::class;
    }

    public function testDefaultActiveFilterFormatter(): void
    {
        $filter = $this->createFilter();

        $formatter = $filter->getConfig()->getOption('active_filter_formatter');

        $this->assertEquals(new TranslatableMessage('Yes', domain: 'KreyuDataTable'), $formatter(new FilterData(true)));
        $this->assertEquals(new TranslatableMessage('No', domain: 'KreyuDataTable'), $formatter(new FilterData(false)));
    }

    public function testItShouldAddChoicesAndChoiceTranslationDomainFormOptionsWhenFormTypeIsChoiceType(): void
    {
        $formOptions = ['trim' => false];

        $filter = $this->createFilter(['form_options' => $formOptions]);

        $expectedFormOptions = $formOptions + [
            'choices' => ['Yes' => true, 'No' => false],
            'choice_translation_domain' => 'KreyuDataTable',
        ];

        $this->assertEquals($expectedFormOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotOverwriteChoicesAndChoiceTranslationDomainFormOptionsIfGivenWhenFormTypeIsChoiceType(): void
    {
        $formOptions = [
            'choices' => ['True' => true, 'False' => false],
            'choice_translation_domain' => 'App',
        ];

        $filter = $this->createFilter(['form_options' => $formOptions]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotModifyFormOptionsWhenFormTypeIsNotChoiceType(): void
    {
        $formOptions = ['trim' => false];

        $filter = $this->createFilter(['form_type' => TextType::class, 'form_options' => $formOptions]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }
}
