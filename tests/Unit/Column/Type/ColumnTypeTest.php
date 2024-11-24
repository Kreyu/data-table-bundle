<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Model\User;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnTypeTest extends ColumnTypeTestCase
{
    use ReflectionTrait;

    private ?TranslatorInterface $translator = null;

    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new ColumnType($this->translator);
    }

    public function testDefaultLabelInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName');

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals('First name', $headerView->vars['label']);
    }

    public function testDefaultExportLabelInheritsFromLabel(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'label' => 'Name',
            'export' => true,
        ]);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals('Name', $exportHeaderView->vars['label']);
    }

    public function testDefaultExportLabelInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => true,
        ]);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals('First name', $exportHeaderView->vars['label']);
    }

    public function testPassingLabelOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'label' => 'Name',
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals('Name', $headerView->vars['label']);
    }

    public function testPassingExportLabelOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'label' => 'Name',
            ],
        ]);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals('Name', $exportHeaderView->vars['label']);
    }

    public function testPassingExportLabelOptionAsTranslatable(): void
    {
        $translatable = $this->createTranslatable(value: 'First name');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'label' => $translatable,
            ],
        ]);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals('First name', $exportHeaderView->vars['label']);
    }

    public function testPassingExportLabelOptionAsTranslatableWithoutTranslator(): void
    {
        $translatable = $this->createTranslatable(value: 'First name', expectTranslated: false);

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'label' => $translatable,
            ],
        ]);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals($translatable, $exportHeaderView->vars['label']);
    }

    #[DataProvider('provideExportLabelTranslationOptions')]
    public function testExportLabelTranslation(array $options): void
    {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->once())->method('trans')
            ->with('%first_name%', ['%first_name%' => 'John'], 'user')
            ->willReturn('John')
        ;

        $column = $this->createNamedColumn('firstName', $options);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals('John', $exportHeaderView->vars['label']);
    }

    public static function provideExportLabelTranslationOptions(): iterable
    {
        yield 'inherit all' => [
            [
                'label' => '%first_name%',
                'header_translation_domain' => 'user',
                'header_translation_parameters' => ['%first_name%' => 'John'],
                'export' => true,
            ],
        ];

        yield 'inherit except parameters' => [
            [
                'label' => '%first_name%',
                'header_translation_domain' => 'user',
                'header_translation_parameters' => ['%first_name%' => 'Jane'],
                'export' => [
                    'header_translation_parameters' => ['%first_name%' => 'John'],
                ],
            ],
        ];

        yield 'inherit except domain' => [
            [
                'label' => '%first_name%',
                'header_translation_domain' => 'messages',
                'header_translation_parameters' => ['%first_name%' => 'John'],
                'export' => [
                    'header_translation_domain' => 'user',
                ],
            ],
        ];

        yield 'inherit except label' => [
            [
                'label' => '%first_name% %last_name%',
                'header_translation_domain' => 'user',
                'header_translation_parameters' => ['%first_name%' => 'John'],
                'export' => [
                    'label' => '%first_name%',
                ],
            ],
        ];
    }

    public function testPassingExportLabelOptionWithTranslatorInheritsTranslationDomain(): void
    {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->once())->method('trans')->with(
            '%first_name%', ['%first_name%' => 'John'], 'user',
        );

        $column = $this->createNamedColumn('firstName', [
            'header_translation_domain' => 'user',
            'export' => [
                'label' => '%first_name%',
                'header_translation_parameters' => ['%first_name%' => 'John'],
            ],
        ]);

        $column->createExportHeaderView($this->createHeaderRowView());
    }

    public function testHeaderTranslationDomainDefaultsToDataTableTranslationDomainOption(): void
    {
        $column = $this->createNamedColumn('firstName');

        $dataTableView = new DataTableView();
        $dataTableView->vars['translation_domain'] = 'user';

        $headerView = $column->createHeaderView($this->createHeaderRowView($dataTableView));

        $this->assertEquals('user', $headerView->vars['translation_domain']);
    }

    public function testPassingHeaderTranslationDomainOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'header_translation_domain' => 'user',
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals('user', $headerView->vars['translation_domain']);
    }

    public function testPassingHeaderTranslationParametersOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'header_translation_parameters' => ['%first_name%' => 'John'],
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals(['%first_name%' => 'John'], $headerView->vars['translation_parameters']);
    }

    public function testPassingValueTranslationDomainAsNullDefaultsToDataTableTranslationDomain(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => null,
        ]);

        $dataTableView = new DataTableView();
        $dataTableView->vars['translation_domain'] = 'product';

        $valueView = $column->createValueView($this->createValueRowView($dataTableView));

        $this->assertEquals('product', $valueView->vars['translation_domain']);
    }

    public function testPassingValueTranslationDomainOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => 'product',
        ]);

        $valueView = $column->createValueView($this->createValueRowView());

        $this->assertEquals('product', $valueView->vars['translation_domain']);
    }

    public function testPassingValueTranslationParametersOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_parameters' => ['%first_name%' => 'John'],
        ]);

        $valueView = $column->createValueView($this->createValueRowView());

        $this->assertEquals(['%first_name%' => 'John'], $valueView->vars['translation_parameters']);
    }

    public function testPassingCallableValueTranslationParametersOption(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'value_translation_parameters' => function (string $value, User $data) use ($user) {
                $this->assertEquals('John', $value);
                $this->assertEquals($user, $data);

                return ['%first_name%' => $value];
            },
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: $user));

        $this->assertEquals(['%first_name%' => 'John'], $valueView->vars['translation_parameters']);
    }

    public function testPassingCallableExportValueTranslationParametersOption(): void
    {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->once())->method('trans')
            ->with('%first_name%', ['%first_name%' => 'John'], 'user')
            ->willReturn('John')
        ;

        $user = new User(firstName: '%first_name%');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'value_translation_domain' => 'user',
                'value_translation_parameters' => function (string $value, User $data) use ($user) {
                    $this->assertEquals('%first_name%', $value);
                    $this->assertEquals($user, $data);

                    return ['%first_name%' => 'John'];
                },
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals('John', $exportValueView->vars['value']);
    }

    public function testTranslatableExportValue()
    {
        $firstName = $this->createTranslatable(value: 'John');

        $user = new User(firstName: $firstName);

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'value_translation_domain' => 'user',
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertSame($firstName, $exportValueView->vars['data']);
        $this->assertEquals('John', $exportValueView->vars['value']);
    }

    public function testNonStringExportValueNotTranslated()
    {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->never())->method('trans');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'value_translation_domain' => 'user',
            ],
        ]);

        $user = new User(firstName: null);

        $exportHeaderView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertSame($user->firstName, $exportHeaderView->vars['data']);
        $this->assertSame($user->firstName, $exportHeaderView->vars['value']);
    }

    #[DataProvider('provideExportValueTranslationOptions')]
    public function testExportValueTranslation(array $options): void
    {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->once())->method('trans')
            ->with('%first_name%', ['%first_name%' => 'John'], 'user')
            ->willReturn('John')
        ;

        $column = $this->createNamedColumn('firstName', $options);

        $user = new User(firstName: '%first_name%');

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals('%first_name%', $exportValueView->vars['data']);
        $this->assertEquals('John', $exportValueView->vars['value']);
    }

    public static function provideExportValueTranslationOptions(): iterable
    {
        yield 'inherit all' => [
            [
                'value_translation_domain' => 'user',
                'value_translation_parameters' => ['%first_name%' => 'John'],
                'export' => true,
            ],
        ];

        yield 'inherit except parameters' => [
            [
                'value_translation_domain' => 'user',
                'value_translation_parameters' => ['%last_name%' => 'Jane'],
                'export' => [
                    'value_translation_parameters' => ['%first_name%' => 'John'],
                ],
            ],
        ];

        yield 'inherit except domain' => [
            [
                'value_translation_domain' => 'messages',
                'value_translation_parameters' => ['%first_name%' => 'John'],
                'export' => [
                    'value_translation_domain' => 'user',
                ],
            ],
        ];
    }

    public function testPassingBlockPrefixOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'block_prefix' => 'first_name',
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());
        $valueView = $column->createValueView($this->createValueRowView());

        $this->assertEquals(['first_name', 'column'], $headerView->vars['block_prefixes']);
        $this->assertEquals(['first_name', 'column'], $valueView->vars['block_prefixes']);
    }

    public function testPassingSortOptionAsBoolean(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'sort' => true,
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertTrue($column->getConfig()->isSortable());
        $this->assertTrue($headerView->vars['sortable']);
        $this->assertEquals('firstName', (string) $column->getConfig()->getSortPropertyPath());
    }

    public function testPassingSortOptionAsString(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'sort' => 'user.firstName',
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals('user.firstName', (string) $column->getConfig()->getSortPropertyPath());
        $this->assertEquals('user.firstName', $headerView->vars['sort_field']);
    }

    public function testPassingExportOptionAsBoolean(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => true,
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertTrue($column->getConfig()->isExportable());
        $this->assertTrue($headerView->vars['export']);
    }

    public function testPassingExportOptionAsArray(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => ['label' => 'Name'],
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());
        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertTrue($column->getConfig()->isExportable());
        $this->assertTrue($headerView->vars['export']);
        $this->assertEquals('Name', $exportHeaderView->vars['label']);
    }

    public function testPassingFormatterOption(): void
    {
        $user = new User(firstName: 'john');
        
        $column = $this->createNamedColumn('firstName', [
            'formatter' => function (string $value, User $data, ColumnInterface $column, array $options) use ($user) {
                $this->assertEquals($user, $data);
                $this->assertEquals('firstName', $column->getName());
                $this->assertIsCallable($options['formatter']);

                return strtoupper($value);
            },
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: $user));

        $this->assertEquals('john', $valueView->vars['data']);
        $this->assertEquals('JOHN', $valueView->vars['value']);
    }

    public function testPassingExportFormatterOption(): void
    {
        $user = new User(firstName: 'john');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'formatter' => function (string $value, User $data, ColumnInterface $column, array $options) use ($user) {
                    $this->assertEquals($user, $data);
                    $this->assertEquals('firstName', $column->getName());
                    $this->assertIsCallable($options['formatter']);

                    return strtoupper($value);
                },
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals('john', $exportValueView->vars['data']);
        $this->assertEquals('JOHN', $exportValueView->vars['value']);
    }

    public function testFormatterNotAppliedWithNullData(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'formatter' => fn (mixed $value) => throw new \LogicException('This should not be called!'),
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: new User(firstName: null)));

        $this->assertNull($valueView->vars['value']);
    }

    public function testExportFormatterNotAppliedWithNullData(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'formatter' => fn (mixed $value) => throw new \LogicException('This should not be called!'),
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: new User(firstName: null)));

        $this->assertNull($exportValueView->vars['value']);
    }

    public function testDefaultPropertyPathInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName');

        $valueView = $column->createValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('John', $valueView->vars['value']);
        $this->assertEquals('firstName', (string) $column->getConfig()->getPropertyPath());
    }

    public function testExportPropertyPathInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => true,
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('John', $exportValueView->vars['value']);
    }

    public function testExportPropertyPathInheritsFromPropertyPathOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'property_path' => 'firstNameUppercased',
            'export' => true,
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('JOHN', $exportValueView->vars['value']);
    }

    public function testPassingPropertyPathOptionAsString(): void
    {
        $column = $this->createNamedColumn('name', [
            'property_path' => 'firstNameUppercased',
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('JOHN', $valueView->vars['value']);
        $this->assertEquals('firstNameUppercased', (string) $column->getConfig()->getPropertyPath());
    }

    public function testPassingExportPropertyPathOptionAsString(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => 'firstNameUppercased',
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('JOHN', $exportValueView->vars['value']);
    }

    public function testPassingPropertyPathOptionAsObject(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'property_path' => new PropertyPath('firstNameUppercased'),
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('JOHN', $valueView->vars['value']);
        $this->assertEquals('firstNameUppercased', (string) $column->getConfig()->getPropertyPath());
    }

    public function testPassingExportPropertyPathOptionAsObject(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => new PropertyPath('firstNameUppercased'),
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: new User(firstName: 'John')));

        $this->assertEquals('JOHN', $exportValueView->vars['value']);
    }

    public function testPassingPropertyAccessorOption(): void
    {
        $user = new User(firstName: 'John');

        $propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $propertyAccessor->expects($this->once())
            ->method('getValue')
            ->with($user, 'firstName')
        ;

        $column = $this->createNamedColumn('firstName', [
            'property_accessor' => $propertyAccessor,
        ]);

        $column->createValueView($this->createValueRowView(data: $user));
    }

    public function testPassingExportPropertyAccessorOption(): void
    {
        $user = new User(firstName: 'John');

        $propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $propertyAccessor->expects($this->once())
            ->method('getValue')
            ->with($user, 'firstName')
        ;

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_accessor' => $propertyAccessor,
            ],
        ]);

        $column->createExportValueView($this->createValueRowView(data: $user));
    }

    public function testGetterHasHigherPriorityThanPropertyPath(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'property_path' => 'firstNameUppercased',
            'getter' => function (User $data, ColumnInterface $column, array $options) use ($user) {
                $this->assertEquals($user, $data);
                $this->assertEquals('firstName', $column->getName());
                $this->assertEquals('firstNameUppercased', $options['property_path']);

                return 'Definitely not John';
            },
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: $user));

        $this->assertEquals('Definitely not John', $valueView->vars['value']);
    }

    public function testExportGetterHasHigherPriorityThanPropertyPath(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => 'firstNameUppercased',
                'getter' => function (User $data, ColumnInterface $column, array $options) use ($user) {
                    $this->assertEquals($user, $data);
                    $this->assertEquals('firstName', $column->getName());
                    $this->assertEquals('firstNameUppercased', $options['property_path']);

                    return 'Definitely not John';
                },
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals('Definitely not John', $exportValueView->vars['value']);
    }

    public function testExportGetterOptionInheritance(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'getter' => function (User $data, ColumnInterface $column, array $options) use ($user) {
                $this->assertEquals($user, $data);
                $this->assertEquals('firstName', $column->getName());
                $this->assertIsCallable($options['getter']);

                return 'Definitely not John';
            },
            'export' => true,
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals('Definitely not John', $exportValueView->vars['value']);
    }

    public function testPassingExportGetterOption(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'getter' => function (User $data, ColumnInterface $column, array $options) use ($user) {
                    $this->assertEquals($user, $data);
                    $this->assertEquals('firstName', $column->getName());
                    $this->assertIsCallable($options['getter']);

                    return 'Definitely not John';
                },
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals('Definitely not John', $exportValueView->vars['value']);
    }

    public function testWithNeitherPropertyPathNorGetterOption(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'property_path' => false,
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: $user));

        $this->assertEquals($user, $valueView->vars['data']);
        $this->assertEquals($user, $valueView->vars['value']);
    }

    public function testExportWithNeitherPropertyPathNorGetterOption(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => false,
            ],
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertEquals($user, $exportValueView->vars['data']);
        $this->assertEquals($user, $exportValueView->vars['value']);
    }

    public function testPassingHeaderAttrOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'header_attr' => ['class' => 'text-primary'],
        ]);

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals(['class' => 'text-primary'], $headerView->vars['attr']);
    }

    public function testPassingValueAttrOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_attr' => ['class' => 'text-primary'],
        ]);

        $valueView = $column->createValueView($this->createValueRowView());

        $this->assertEquals(['class' => 'text-primary'], $valueView->vars['attr']);
    }

    public function testPassingValueAttrOptionAsCallable(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'value_attr' => function (string $value, User $data) use ($user) {
                $this->assertEquals('John', $value);
                $this->assertEquals($user, $data);

                return ['class' => 'text-danger'];
            },
        ]);

        $valueView = $column->createValueView($this->createValueRowView(data: $user));

        $this->assertEquals(['class' => 'text-danger'], $valueView->vars['attr']);
    }

    public function testPassingPriorityOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'priority' => 10,
        ]);

        $this->assertEquals(10, $column->getPriority());
    }

    public function testPassingVisibleOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'visible' => false,
        ]);

        $this->assertFalse($column->isVisible());
    }

    public function testPassingPersonalizableOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'personalizable' => false,
        ]);

        $this->assertFalse($column->getConfig()->isPersonalizable());
    }

    public function testHeaderViewVarsContainsName(): void
    {
        $column = $this->createNamedColumn('firstName');

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertEquals('firstName', $headerView->vars['name']);
    }

    public function testValueViewVarsContainsName(): void
    {
        $column = $this->createNamedColumn('firstName');

        $valueView = $column->createValueView($this->createValueRowView());

        $this->assertEquals('firstName', $valueView->vars['name']);
    }

    public function testHeaderViewVarsContainsItself()
    {
        $column = $this->createNamedColumn('firstName');

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertSame($headerView, $headerView->vars['column']);
    }

    public function testValueViewVarsContainsItself()
    {
        $column = $this->createColumn();

        $valueView = $column->createValueView($this->createValueRowView());

        $this->assertSame($valueView, $valueView->vars['column']);
    }

    public function testHeaderViewVarsContainsHeaderRow()
    {
        $column = $this->createColumn();

        $headerView = $column->createHeaderView($headerRow = $this->createHeaderRowView());

        $this->assertSame($headerRow, $headerView->vars['row']);
    }

    public function testValueViewVarsContainsValueRow()
    {
        $column = $this->createColumn();

        $valueView = $column->createValueView($valueRow = $this->createValueRowView());

        $this->assertSame($valueRow, $valueView->vars['row']);
    }

    public function testHeaderViewVarsContainsDataTable(): void
    {
        $column = $this->createColumn();

        $headerView = $column->createHeaderView($this->createHeaderRowView($dataTableView = new DataTableView()));

        $this->assertSame($dataTableView, $headerView->vars['data_table']);
    }

    public function testValueViewVarsContainsDataTable(): void
    {
        $column = $this->createColumn();

        $valueView = $column->createValueView($this->createValueRowView($dataTableView = new DataTableView()));

        $this->assertSame($dataTableView, $valueView->vars['data_table']);
    }

    public function testHeaderViewVarsContainsSortParameterName(): void
    {
        $column = $this->createColumn();

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertSame('sort_data_table', $headerView->vars['sort_parameter_name']);
    }

    public function testHeaderViewVarsContainsSortingData(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'sort' => true,
        ]);

        $this->dataTable->addColumn($column);
        $this->dataTable->sort(SortingData::fromArray(['firstName' => 'desc']));

        $headerView = $column->createHeaderView($this->createHeaderRowView());

        $this->assertTrue($headerView->vars['sorted']);
        $this->assertEquals('desc', $headerView->vars['sort_direction']);
    }

    public function testBuildExportHeaderViewWithNonExportableColumn()
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => false,
        ]);

        $exportHeaderView = $column->createExportHeaderView($this->createHeaderRowView());

        $this->assertEquals(['attr' => []], $exportHeaderView->vars);
    }

    public function testBuildExportValueViewWithNonExportableColumn()
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => false,
        ]);

        $exportValueView = $column->createExportValueView($this->createValueRowView());

        $this->assertEquals(['attr' => []], $exportValueView->vars);
    }

    public function testBlockPrefixesWithParent()
    {
        $parent = $this->createMock(ResolvedColumnTypeInterface::class);
        $parent->method('getBlockPrefix')->willReturn('parent');

        $column = $this->createNamedColumn('firstName', [
            'block_prefix' => 'first_name',
        ]);

        $this->setPrivatePropertyValue($column->getConfig()->getType(), 'parent', $parent);

        $headerView = $column->createHeaderView($this->createHeaderRowView());
        $valueView = $column->createValueView($this->createValueRowView());

        $expectedBlockPrefixes = ['first_name', 'column', 'parent'];

        $this->assertEquals($expectedBlockPrefixes, $headerView->vars['block_prefixes']);
        $this->assertEquals($expectedBlockPrefixes, $valueView->vars['block_prefixes']);
    }

    private function createHeaderRowView(?DataTableView $dataTableView = null): HeaderRowView
    {
        return new HeaderRowView($dataTableView ?? new DataTableView());
    }

    private function createValueRowView(?DataTableView $dataTableView = null, mixed $data = null): ValueRowView
    {
        return new ValueRowView($dataTableView ?? new DataTableView(), 0, $data);
    }

    protected function createTranslator(): MockObject&TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);

        if (method_exists(TranslatableInterface::class, 'getLocale')) {
            $translator->method('getLocale')->willReturn('en');
        }

        return $translator;
    }

    protected function createTranslatable(string $value, bool $expectTranslated = true): MockObject&TranslatableInterface
    {
        $translatable = $this->createMock(TranslatableInterface::class);

        if ($expectTranslated) {
            $this->translator ??= $this->createTranslator();

            $locale = null;

            if (method_exists(TranslatableInterface::class, 'getLocale')) {
                $locale = 'en';
            }

            $translatable->expects($this->once())
                ->method('trans')
                ->with($this->translator, $locale)
                ->willReturn($value)
            ;

            return $translatable;
        }

        $translatable->expects($this->never())->method('trans');

        return $translatable;
    }
}
