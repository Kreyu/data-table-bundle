<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Integration;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
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

        // Booting the kernel leaves one exception handler registered; drop it for PHPUnit's strict check.
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
        $this->assertInstanceOf(DataTableFactoryInterface::class, self::getContainer()->get(DataTableFactoryInterface::class));
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
}
