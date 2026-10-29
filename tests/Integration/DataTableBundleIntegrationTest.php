<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Integration;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

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

        $this->assertInstanceOf(DataTableInterface::class, $factory->create(DataTableType::class, []));
    }

    public function testTwigDataTableExtensionIsRegistered(): void
    {
        self::bootKernel(['debug' => false]);

        $this->assertTrue(self::getContainer()->has('kreyu_data_table.twig.data_table_extension'));
    }

    public function testUrlQueryParametersKeepSearchTerm(): void
    {
        self::bootKernel(['debug' => false]);

        /** @var DataTableFactoryInterface $factory */
        $factory = self::getContainer()->get(DataTableFactoryInterface::class);

        $builder = $factory->createBuilder(DataTableType::class, [['id' => 1]], ['filtration_enabled' => true]);
        $builder->addColumn('id');
        $builder->setSearchHandler(static fn () => null);

        $dataTable = $builder->getDataTable();
        $dataTable->filter(FiltrationData::fromArray([DataTableBuilderInterface::SEARCH_FILTER_NAME => 'foo']));

        /** @var array<string, array<string, array{value: mixed}>> $urlQueryParameters */
        $urlQueryParameters = $dataTable->createView()->vars['url_query_parameters'];
        $filtrationParameters = $urlQueryParameters[$dataTable->getConfig()->getFiltrationParameterName()] ?? [];

        $this->assertSame('foo', $filtrationParameters[DataTableBuilderInterface::SEARCH_FILTER_NAME]['value'] ?? null);
    }
}
