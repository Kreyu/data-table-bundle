<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Integration;

use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class DataTableBundleIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return DataTableTestKernel::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public static function tearDownAfterClass(): void
    {
        (new Filesystem())->remove(DataTableTestKernel::tempDir());
    }

    public function testKernelBootsAndCompilesTheContainer(): void
    {
        self::bootKernel(['debug' => false]);

        $this->assertTrue(self::getContainer()->has(DataTableFactoryInterface::class));
        $this->assertInstanceOf(
            DataTableFactoryInterface::class,
            self::getContainer()->get(DataTableFactoryInterface::class),
        );
    }

    public function testFactoryCreatesADataTable(): void
    {
        self::bootKernel(['debug' => false]);

        $factory = self::getContainer()->get(DataTableFactoryInterface::class);
        $this->assertInstanceOf(DataTableFactoryInterface::class, $factory);

        $this->assertInstanceOf(DataTableInterface::class, $factory->create(DataTableType::class, []));
    }

    public function testTwigDataTableExtensionIsRegistered(): void
    {
        self::bootKernel(['debug' => false]);

        $this->assertTrue(self::getContainer()->has('kreyu_data_table.twig.data_table_extension'));
    }

    public function testColumnHeaderFilterIsPartOfTheFiltrationForm(): void
    {
        self::bootKernel(['debug' => false]);

        $appliedValues = [];
        $recordAppliedValue = static function (
            ProxyQueryInterface $query,
            FilterData $data,
            FilterInterface $filter,
        ) use (&$appliedValues): void {
            $appliedValues[$filter->getName()] = $data->getValue();
        };

        $factory = self::getContainer()->get(DataTableFactoryInterface::class);
        $this->assertInstanceOf(DataTableFactoryInterface::class, $factory);

        $builder = $factory->createNamedBuilder('products', DataTableType::class, new ArrayProxyQuery([]), [
            'personalization_enabled' => true,
        ]);
        $builder
            ->addColumn('name', TextColumnType::class, [
                'filter' => CallbackFilterType::class,
                'filter_options' => ['callback' => $recordAppliedValue],
            ])
            ->addColumn('author.name', TextColumnType::class, [
                'filter' => CallbackFilterType::class,
                'filter_options' => ['callback' => $recordAppliedValue],
            ])
            ->addFilter('city', CallbackFilterType::class, ['callback' => $recordAppliedValue])
        ;

        $dataTable = $builder->getDataTable();
        $dataTable->handleRequest(new Request([
            'filter_products' => [
                'name' => ['value' => 'Ann'],
                'author:name' => ['value' => 'Bob'],
                'city' => ['value' => 'Paris'],
            ],
        ]));

        $this->assertSame(['city' => 'Paris', 'name' => 'Ann', 'author.name' => 'Bob'], $appliedValues);

        $view = $dataTable->createView();
        $headerRow = $view->headerRow;
        $this->assertNotNull($headerRow);

        /** @var FormView $filtrationForm */
        $filtrationForm = $view->vars['filtration_form'];

        /** @var FormView $headerFilterForm */
        $headerFilterForm = $headerRow->offsetGet('name')->vars['filter_form'];

        $this->assertSame(['city'], array_keys($filtrationForm->children));
        $this->assertSame($filtrationForm, $headerFilterForm->parent);
        $this->assertSame('filter_products[name][value]', $headerFilterForm['value']->vars['full_name']);
        $this->assertSame(
            [
                'filter_products' => [
                    'city' => ['value' => 'Paris'],
                    'name' => ['value' => 'Ann'],
                    'author:name' => ['value' => 'Bob'],
                ],
                'page_products' => 1,
                'limit_products' => 25,
            ],
            $view->vars['url_query_parameters'],
        );

        /** @var array<string, string> $headerFilterValueAttr */
        $headerFilterValueAttr = $headerFilterForm['value']->vars['attr'];

        $this->assertSame($filtrationForm->vars['id'], $headerFilterValueAttr['form']);

        $hiddenNameColumn = new PersonalizationColumnData('name', visible: false);
        $dataTable->personalize(new PersonalizationData([$hiddenNameColumn]));

        $personalizedView = $dataTable->createView();

        /** @var FormView $personalizedFiltrationForm */
        $personalizedFiltrationForm = $personalizedView->vars['filtration_form'];

        $this->assertSame(['city', 'name'], array_keys($personalizedFiltrationForm->children));
        /** @var array<string, FormView> $personalizedHeaderFields */
        $personalizedHeaderFields = $personalizedFiltrationForm->vars['header_fields'];

        $this->assertSame(['author:name'], array_keys($personalizedHeaderFields));
    }
}
