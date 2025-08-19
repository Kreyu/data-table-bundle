<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Model\User;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Translation\TranslatableMessage;
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

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals('First name', $columnHeaderView->vars['label']);
    }

    public function testDefaultExportLabelInheritsFromLabel(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'label' => 'Name',
            'export' => true,
        ]);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals('Name', $exportColumnHeaderView->vars['label']);
    }

    public function testDefaultExportLabelInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => true,
        ]);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals('First name', $exportColumnHeaderView->vars['label']);
    }

    public function testPassingLabelOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'label' => 'Name',
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals('Name', $columnHeaderView->vars['label']);
    }

    public function testPassingExportLabelOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'label' => 'Name',
            ],
        ]);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals('Name', $exportColumnHeaderView->vars['label']);
    }

    public function testPassingExportLabelOptionAsTranslatable(): void
    {
        $translatable = $this->createTranslatable(value: 'First name');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'label' => $translatable,
            ],
        ]);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals('First name', $exportColumnHeaderView->vars['label']);
    }

    public function testPassingExportLabelOptionAsTranslatableWithoutTranslator(): void
    {
        $translatable = $this->createTranslatable(value: 'First name', expectTranslated: false);

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'label' => $translatable,
            ],
        ]);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals($translatable, $exportColumnHeaderView->vars['label']);
    }

    #[DataProvider('provideExportLabelTranslationOptions')]
    public function testExportLabelTranslation(array $options): void
    {
        $this->expectTranslation('John', '%first_name%', ['%first_name%' => 'John'], 'user');

        $column = $this->createNamedColumn('firstName', $options);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals('John', $exportColumnHeaderView->vars['label']);
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
        $this->expectTranslation('John', '%first_name%', ['%first_name%' => 'John'], 'user');

        $column = $this->createNamedColumn('firstName', [
            'header_translation_domain' => 'user',
            'export' => [
                'label' => '%first_name%',
                'header_translation_parameters' => ['%first_name%' => 'John'],
            ],
        ]);

        $this->createExportColumnHeaderView($column);
    }

    public function testHeaderTranslationDomainDefaultsToDataTableTranslationDomainOption(): void
    {
        $column = $this->createNamedColumn('firstName');

        $dataTableView = new DataTableView();
        $dataTableView->vars['translation_domain'] = 'user';

        $columnHeaderView = $this->createColumnHeaderView($column, $this->createHeaderRowView($dataTableView));

        $this->assertEquals('user', $columnHeaderView->vars['translation_domain']);
    }

    public function testPassingHeaderTranslationDomainOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'header_translation_domain' => 'user',
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals('user', $columnHeaderView->vars['translation_domain']);
    }

    public function testPassingHeaderTranslationParametersOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'header_translation_parameters' => ['%first_name%' => 'John'],
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals(['%first_name%' => 'John'], $columnHeaderView->vars['translation_parameters']);
    }

    public function testPassingValueTranslationKeyOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_key' => 'first_name',
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals('first_name', $columnValueView->vars['translation_key']);
    }

    public function testDefaultValueTranslationKeyWhenColumnIsNotTranslatableAndValueIsString(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName');

        $columnValueView = $this->createColumnValueView($column, data: $user);

        $this->assertNull($columnValueView->vars['translation_key']);
    }

    public function testDefaultValueTranslationKeyWhenColumnIsTranslatableAndValueIsString(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => 'user',
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals('John', $columnValueView->vars['translation_key']);
    }

    public function testDefaultValueTranslationKeyWhenColumnIsTranslatableAndValueIsTranslatable(): void
    {
        $user = new User(firstName: new TranslatableMessage('John'));

        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => 'user',
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals(new TranslatableMessage('John'), $columnValueView->vars['translation_key']);
    }

    public function testPassingValueTranslationDomainAsNullDefaultsToDataTableTranslationDomain(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => null,
        ]);

        $dataTableView = new DataTableView();
        $dataTableView->vars['translation_domain'] = 'product';

        $columnValueView = $this->createColumnValueView($column, $this->createValueRowView($dataTableView));

        $this->assertEquals('product', $columnValueView->vars['translation_domain']);
    }

    public function testPassingValueTranslationDomainOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => 'product',
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals('product', $columnValueView->vars['translation_domain']);
    }

    public function testPassingValueTranslationParametersOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_translation_parameters' => ['%first_name%' => 'John'],
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals(['%first_name%' => 'John'], $columnValueView->vars['translation_parameters']);
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

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals(['%first_name%' => 'John'], $columnValueView->vars['translation_parameters']);
    }

    public function testPassingCallableExportValueTranslationParametersOption(): void
    {
        $this->expectTranslation('John', '%first_name%', ['%first_name%' => 'John'], 'user');

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

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals('John', $exportColumnValueView->vars['value']);
    }

    public function testValueTranslationVarsWhenValueIsString()
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName');

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertFalse($columnValueView->vars['translatable']);
        $this->assertFalse($columnValueView->vars['is_instance_of_translatable']);
    }

    public function testValueTranslationVarsWhenValueIsTranslatable()
    {
        $user = new User(firstName: new TranslatableMessage('John'));

        $column = $this->createNamedColumn('firstName');

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertTrue($columnValueView->vars['translatable']);
        $this->assertTrue($columnValueView->vars['is_instance_of_translatable']);
    }

    public function testValueTranslationVarsWhenTranslationDomainOptionIsGiven()
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'value_translation_domain' => 'user',
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertTrue($columnValueView->vars['translatable']);
        $this->assertFalse($columnValueView->vars['is_instance_of_translatable']);
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

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertSame($firstName, $exportColumnValueView->vars['data']);
        $this->assertEquals('John', $exportColumnValueView->vars['value']);
    }

    public function testNonStringExportValueNotTranslated()
    {
        $this->expectNoTranslation();

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'value_translation_domain' => 'user',
            ],
        ]);

        $user = new User(firstName: null);

        $exportColumnHeaderView = $column->createExportValueView($this->createValueRowView(data: $user));

        $this->assertSame($user->firstName, $exportColumnHeaderView->vars['data']);
        $this->assertSame($user->firstName, $exportColumnHeaderView->vars['value']);
    }

    #[DataProvider('provideExportValueTranslationOptions')]
    public function testExportValueTranslation(array $options): void
    {
        $this->expectTranslation('John', '%first_name%', ['%first_name%' => 'John'], 'user');

        $column = $this->createNamedColumn('firstName', $options);

        $user = new User(firstName: '%first_name%');

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals('%first_name%', $exportColumnValueView->vars['data']);
        $this->assertEquals('John', $exportColumnValueView->vars['value']);
    }

    public static function provideExportValueTranslationOptions(): iterable
    {
        yield 'inherit all' => [
            [
                'value_translation_key' => '%first_name%',
                'value_translation_domain' => 'user',
                'value_translation_parameters' => ['%first_name%' => 'John'],
                'export' => true,
            ],
        ];

        yield 'inherit without translation key' => [
            [
                'value_translation_domain' => 'user',
                'value_translation_parameters' => ['%first_name%' => 'John'],
                'export' => true,
            ],
        ];

        yield 'inherit except key' => [
            [
                'value_translation_key' => '%last_name%',
                'value_translation_domain' => 'user',
                'value_translation_parameters' => ['%first_name%' => 'John'],
                'export' => [
                    'value_translation_key' => '%first_name%',
                ],
            ],
        ];

        yield 'inherit except parameters' => [
            [
                'value_translation_key' => '%first_name%',
                'value_translation_domain' => 'user',
                'value_translation_parameters' => ['%last_name%' => 'Jane'],
                'export' => [
                    'value_translation_parameters' => ['%first_name%' => 'John'],
                ],
            ],
        ];

        yield 'inherit except domain' => [
            [
                'value_translation_key' => '%first_name%',
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

        $columnHeaderView = $this->createColumnHeaderView($column);
        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals(['first_name', 'column'], $columnHeaderView->vars['block_prefixes']);
        $this->assertEquals(['first_name', 'column'], $columnValueView->vars['block_prefixes']);
    }

    public function testPassingSortOptionAsBoolean(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'sort' => true,
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertTrue($column->getConfig()->isSortable());
        $this->assertTrue($columnHeaderView->vars['sortable']);
        $this->assertEquals('firstName', (string) $column->getConfig()->getSortPropertyPath());
    }

    public function testPassingSortOptionAsString(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'sort' => 'user.firstName',
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals('user.firstName', (string) $column->getConfig()->getSortPropertyPath());
        $this->assertEquals('user.firstName', $columnHeaderView->vars['sort_field']);
    }

    public function testPassingExportOptionAsBoolean(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => true,
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertTrue($column->getConfig()->isExportable());
        $this->assertTrue($columnHeaderView->vars['export']);
    }

    public function testPassingExportOptionAsArray(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => ['label' => 'Name'],
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);
        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertTrue($column->getConfig()->isExportable());
        $this->assertTrue($columnHeaderView->vars['export']);
        $this->assertEquals('Name', $exportColumnHeaderView->vars['label']);
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

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals('john', $columnValueView->vars['data']);
        $this->assertEquals('JOHN', $columnValueView->vars['value']);
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

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals('john', $exportColumnValueView->vars['data']);
        $this->assertEquals('JOHN', $exportColumnValueView->vars['value']);
    }

    public function testFormatterNotAppliedWithNullData(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'formatter' => fn (mixed $value) => throw new \LogicException('This should not be called!'),
        ]);

        $columnValueView = $this->createColumnValueView($column, data: new User(firstName: null));

        $this->assertNull($columnValueView->vars['value']);
    }

    public function testExportFormatterNotAppliedWithNullData(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'formatter' => fn (mixed $value) => throw new \LogicException('This should not be called!'),
            ],
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: new User(firstName: null));

        $this->assertNull($exportColumnValueView->vars['value']);
    }

    public function testDefaultPropertyPathInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName');

        $columnValueView = $this->createColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('John', $columnValueView->vars['value']);
        $this->assertEquals('firstName', (string) $column->getConfig()->getPropertyPath());
    }

    public function testExportPropertyPathInheritsFromName(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => true,
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('John', $exportColumnValueView->vars['value']);
    }

    public function testExportPropertyPathInheritsFromPropertyPathOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'property_path' => 'firstNameUppercased',
            'export' => true,
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('JOHN', $exportColumnValueView->vars['value']);
    }

    public function testPassingPropertyPathOptionAsString(): void
    {
        $column = $this->createNamedColumn('name', [
            'property_path' => 'firstNameUppercased',
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('JOHN', $columnValueView->vars['value']);
        $this->assertEquals('firstNameUppercased', (string) $column->getConfig()->getPropertyPath());
    }

    public function testPassingExportPropertyPathOptionAsString(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => 'firstNameUppercased',
            ],
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('JOHN', $exportColumnValueView->vars['value']);
    }

    public function testPassingPropertyPathOptionAsObject(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'property_path' => new PropertyPath('firstNameUppercased'),
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('JOHN', $columnValueView->vars['value']);
        $this->assertEquals('firstNameUppercased', (string) $column->getConfig()->getPropertyPath());
    }

    public function testPassingExportPropertyPathOptionAsObject(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => new PropertyPath('firstNameUppercased'),
            ],
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: new User(firstName: 'John'));

        $this->assertEquals('JOHN', $exportColumnValueView->vars['value']);
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

        $this->createColumnValueView($column, rowData: $user);
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

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals('Definitely not John', $columnValueView->vars['value']);
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

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals('Definitely not John', $exportColumnValueView->vars['value']);
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

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals('Definitely not John', $exportColumnValueView->vars['value']);
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

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals('Definitely not John', $exportColumnValueView->vars['value']);
    }

    public function testWithNeitherPropertyPathNorGetterOption(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'property_path' => false,
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals($user, $columnValueView->vars['data']);
        $this->assertEquals($user, $columnValueView->vars['value']);
    }

    public function testExportWithNeitherPropertyPathNorGetterOption(): void
    {
        $user = new User(firstName: 'John');

        $column = $this->createNamedColumn('firstName', [
            'export' => [
                'property_path' => false,
            ],
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $user);

        $this->assertEquals($user, $exportColumnValueView->vars['data']);
        $this->assertEquals($user, $exportColumnValueView->vars['value']);
    }

    public function testPassingHeaderAttrOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'header_attr' => ['class' => 'text-primary'],
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals(['class' => 'text-primary'], $columnHeaderView->vars['attr']);
    }

    public function testPassingValueAttrOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'value_attr' => ['class' => 'text-primary'],
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals(['class' => 'text-primary'], $columnValueView->vars['attr']);
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

        $columnValueView = $this->createColumnValueView($column, rowData: $user);

        $this->assertEquals(['class' => 'text-danger'], $columnValueView->vars['attr']);
    }

    public function testPassingPriorityOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'priority' => 10,
        ]);

        $this->assertEquals(10, $column->getConfig()->getPriority());
    }

    public function testPassingVisibleOption(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'visible' => false,
        ]);

        $this->assertFalse($column->getConfig()->isVisible());
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

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertEquals('firstName', $columnHeaderView->vars['name']);
    }

    public function testValueViewVarsContainsName(): void
    {
        $column = $this->createNamedColumn('firstName');

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals('firstName', $columnValueView->vars['name']);
    }

    public function testHeaderViewVarsContainsItself()
    {
        $column = $this->createNamedColumn('firstName');

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertSame($columnHeaderView, $columnHeaderView->vars['column']);
    }

    public function testValueViewVarsContainsItself()
    {
        $column = $this->createColumn();

        $columnValueView = $this->createColumnValueView($column);

        $this->assertSame($columnValueView, $columnValueView->vars['column']);
    }

    public function testHeaderViewVarsContainsHeaderRow()
    {
        $column = $this->createColumn();

        $columnHeaderView = $column->createHeaderView($headerRow = $this->createHeaderRowView());

        $this->assertSame($headerRow, $columnHeaderView->vars['row']);
    }

    public function testValueViewVarsContainsValueRow()
    {
        $column = $this->createColumn();

        $columnValueView = $this->createColumnValueView($column);

        $this->assertSame($columnValueView->parent, $columnValueView->vars['row']);
    }

    public function testHeaderViewVarsContainsDataTable(): void
    {
        $column = $this->createColumn();

        $columnHeaderView = $column->createHeaderView($this->createHeaderRowView($dataTableView = new DataTableView()));

        $this->assertSame($dataTableView, $columnHeaderView->vars['data_table']);
    }

    public function testValueViewVarsContainsDataTable(): void
    {
        $column = $this->createColumn();

        $columnValueView = $this->createColumnValueView($column, $this->createValueRowView($dataTableView = new DataTableView()));

        $this->assertSame($dataTableView, $columnValueView->vars['data_table']);
    }

    public function testHeaderViewVarsContainsSortParameterName(): void
    {
        $column = $this->createColumn();

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertSame('sort_data_table', $columnHeaderView->vars['sort_parameter_name']);
    }

    public function testHeaderViewVarsContainsSortingData(): void
    {
        $column = $this->createNamedColumn('firstName', [
            'sort' => true,
        ]);

        $this->dataTable->addColumn($column);
        $this->dataTable->sort(SortingData::fromArray(['firstName' => 'desc']));

        $columnHeaderView = $this->createColumnHeaderView($column);

        $this->assertTrue($columnHeaderView->vars['sorted']);
        $this->assertEquals('desc', $columnHeaderView->vars['sort_direction']);
    }

    public function testBuildExportHeaderViewWithNonExportableColumn()
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => false,
        ]);

        $exportColumnHeaderView = $this->createExportColumnHeaderView($column);

        $this->assertEquals(['attr' => []], $exportColumnHeaderView->vars);
    }

    public function testBuildExportValueViewWithNonExportableColumn()
    {
        $column = $this->createNamedColumn('firstName', [
            'export' => false,
        ]);

        $exportColumnValueView = $this->createExportColumnValueView($column);

        $this->assertEquals(['attr' => []], $exportColumnValueView->vars);
    }

    public function testBlockPrefixesWithParent()
    {
        $parent = $this->createMock(ResolvedColumnTypeInterface::class);
        $parent->method('getBlockPrefix')->willReturn('parent');

        $column = $this->createNamedColumn('firstName', [
            'block_prefix' => 'first_name',
        ]);

        $this->setPrivatePropertyValue($column->getConfig()->getType(), 'parent', $parent);

        $columnHeaderView = $this->createColumnHeaderView($column);
        $columnValueView = $this->createColumnValueView($column);

        $expectedBlockPrefixes = ['first_name', 'column', 'parent'];

        $this->assertEquals($expectedBlockPrefixes, $columnHeaderView->vars['block_prefixes']);
        $this->assertEquals($expectedBlockPrefixes, $columnValueView->vars['block_prefixes']);
    }

    protected function expectTranslation(
        string $expected,
        string $id,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null,
    ): void {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->once())
            ->method('trans')
            ->with($id, $parameters, $domain, $locale)
            ->willReturn($expected);
    }

    protected function expectNoTranslation(): void
    {
        $this->translator = $this->createTranslator();
        $this->translator->expects($this->never())->method('trans');
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
            $this->translator = $this->createTranslator();

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
