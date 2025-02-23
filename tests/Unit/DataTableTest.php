<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Test\DataTableIntegrationTestCase;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableTest extends DataTableIntegrationTestCase
{
    public function testGetColumns()
    {
        $dataTable = $this->createDataTableBuilder()
            ->addColumn('first', options: ['priority' => 1])
            ->addColumn('second', options: ['priority' => 2])
            ->addColumn('third', options: ['priority' => 3])
            ->addColumn('fourth', options: ['priority' => 4])
            ->addColumn('fifth', options: ['priority' => 5])
            ->getDataTable()
        ;

        $columns = array_keys($dataTable->getColumns());

        $this->assertEquals(['fifth', 'fourth', 'third', 'second', 'first'], $columns);
    }

    public function testGetColumnsRespectsPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => true])
            ->addColumn('first', options: ['priority' => 1])
            ->addColumn('second', options: ['priority' => 2])
            ->addColumn('third', options: ['priority' => 3])
            ->addColumn('fourth', options: ['priority' => 4])
            ->addColumn('fifth', options: ['priority' => 100, 'personalizable' => false])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5],
                'second' => ['priority' => 4],
                'third' => ['priority' => 3],
                'fourth' => ['priority' => 2],
                // Should be ignored, because column has "personalizable" option set to false
                'fifth' => ['priority' => 1],
            ],
        ]));

        $columns = array_keys($dataTable->getColumns());

        $this->assertEquals(['fifth', 'first', 'second', 'third', 'fourth'], $columns);
    }

    public function testGetColumnsIgnoresDisabledPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => false])
            ->addColumn('first', options: ['priority' => 1])
            ->addColumn('second', options: ['priority' => 2])
            ->addColumn('third', options: ['priority' => 3])
            ->addColumn('fourth', options: ['priority' => 4])
            ->addColumn('fifth', options: ['priority' => 5])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5],
                'second' => ['priority' => 4],
                'third' => ['priority' => 3],
                'fourth' => ['priority' => 2],
                'fifth' => ['priority' => 1],
            ],
        ]));

        $columns = array_keys($dataTable->getColumns());

        $this->assertEquals(['fifth', 'fourth', 'third', 'second', 'first'], $columns);
    }

    public function testGetVisibleColumns()
    {
        $dataTable = $this->createDataTableBuilder()
            ->addColumn('first', options: ['priority' => 1, 'visible' => true])
            ->addColumn('second', options: ['priority' => 2, 'visible' => false])
            ->addColumn('third', options: ['priority' => 3, 'visible' => true])
            ->addColumn('fourth', options: ['priority' => 4, 'visible' => false])
            ->addColumn('fifth', options: ['priority' => 5, 'visible' => true])
            ->getDataTable();

        $columns = array_keys($dataTable->getVisibleColumns());

        $this->assertEquals(['fifth', 'third', 'first'], $columns);
    }

    public function testGetVisibleColumnsRespectsPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => true])
            ->addColumn('first', options: ['priority' => 1, 'visible' => true])
            ->addColumn('second', options: ['priority' => 2, 'visible' => false])
            ->addColumn('third', options: ['priority' => 3, 'visible' => true])
            ->addColumn('fourth', options: ['priority' => 4, 'visible' => false])
            ->addColumn('fifth', options: ['priority' => 100, 'visible' => true, 'personalizable' => false])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5, 'visible' => false],
                'second' => ['priority' => 4, 'visible' => true],
                'third' => ['priority' => 3, 'visible' => false],
                'fourth' => ['priority' => 2, 'visible' => true],
                // Should be ignored, because column has "personalizable" option set to false
                'fifth' => ['priority' => 1, 'visible' => false],
            ],
        ]));

        $columns = array_keys($dataTable->getVisibleColumns());

        $this->assertEquals(['fifth', 'second', 'fourth'], $columns);
    }

    public function testGetVisibleColumnsIgnoresDisabledPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => false])
            ->addColumn('first', options: ['priority' => 1, 'visible' => true])
            ->addColumn('second', options: ['priority' => 2, 'visible' => false])
            ->addColumn('third', options: ['priority' => 3, 'visible' => true])
            ->addColumn('fourth', options: ['priority' => 4, 'visible' => false])
            ->addColumn('fifth', options: ['priority' => 5, 'visible' => true])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5, 'visible' => false],
                'second' => ['priority' => 4, 'visible' => true],
                'third' => ['priority' => 3, 'visible' => false],
                'fourth' => ['priority' => 2, 'visible' => true],
                'fifth' => ['priority' => 1, 'visible' => false],
            ],
        ]));

        $columns = array_keys($dataTable->getVisibleColumns());

        $this->assertEquals(['fifth', 'third', 'first'], $columns);
    }

    public function testGetHiddenColumns()
    {
        $dataTable = $this->createDataTableBuilder()
            ->addColumn('first', options: ['visible' => true])
            ->addColumn('second', options: ['visible' => false])
            ->addColumn('third', options: ['visible' => true])
            ->addColumn('fourth', options: ['visible' => false])
            ->addColumn('fifth', options: ['visible' => true])
            ->getDataTable();

        $columns = array_keys($dataTable->getHiddenColumns());

        $this->assertEquals(['second', 'fourth'], $columns);
    }

    public function testGetHiddenColumnsRespectsPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => true])
            ->addColumn('first', options: ['priority' => 1, 'visible' => true])
            ->addColumn('second', options: ['priority' => 2, 'visible' => false])
            ->addColumn('third', options: ['priority' => 3, 'visible' => true])
            ->addColumn('fourth', options: ['priority' => 100, 'visible' => false, 'personalizable' => false])
            ->addColumn('fifth', options: ['priority' => 5, 'visible' => true])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5, 'visible' => false],
                'second' => ['priority' => 4, 'visible' => true],
                'third' => ['priority' => 3, 'visible' => false],
                // Should be ignored, because column has "personalizable" option set to false
                'fourth' => ['priority' => 2, 'visible' => true],
                'fifth' => ['priority' => 1, 'visible' => false],
            ],
        ]));

        $columns = array_keys($dataTable->getHiddenColumns());

        $this->assertEquals(['fourth', 'first', 'third', 'fifth'], $columns);
    }

    public function testGetHiddenColumnsIgnoresDisabledPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => false])
            ->addColumn('first', options: ['priority' => 1, 'visible' => true])
            ->addColumn('second', options: ['priority' => 2, 'visible' => false])
            ->addColumn('third', options: ['priority' => 3, 'visible' => true])
            ->addColumn('fourth', options: ['priority' => 4, 'visible' => false])
            ->addColumn('fifth', options: ['priority' => 5, 'visible' => true])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5, 'visible' => false],
                'second' => ['priority' => 4, 'visible' => true],
                'third' => ['priority' => 3, 'visible' => false],
                'fourth' => ['priority' => 2, 'visible' => true],
                'fifth' => ['priority' => 1, 'visible' => false],
            ],
        ]));

        $columns = array_keys($dataTable->getHiddenColumns());

        $this->assertEquals(['fourth', 'second'], $columns);
    }

    public function testGetExportableColumns()
    {
        $dataTable = $this->createDataTableBuilder()
            ->addColumn('first', options: [
                'priority' => 1,
                'visible' => true,
                'export' => true,
            ])
            ->addColumn('second', options: [
                'priority' => 2,
                'visible' => true,
                'export' => [
                    'visible' => false,
                ],
            ])
            ->addColumn('third', options: [
                'priority' => 3,
                'visible' => true,
                'export' => [
                    'priority' => 100,
                ],
            ])
            ->addColumn('fourth', options: [
                'priority' => 4,
                'visible' => false,
                'export' => [
                    'visible' => true,
                ],
            ])
            ->addColumn('fifth', options: [
                'priority' => 5,
                'visible' => true,
                'export' => false,
            ])
            ->getDataTable();

        $columns = array_keys($dataTable->getExportableColumns());

        $this->assertEquals(['third', 'fourth', 'first'], $columns);
    }

    public function testGetExportableColumnsRespectsPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => true])
            ->addColumn('first', options: [
                'priority' => 1,
                'visible' => true,
                'export' => true,
            ])
            ->addColumn('second', options: [
                'priority' => 2,
                'visible' => true,
                'export' => [
                    'visible' => false,
                ],
            ])
            ->addColumn('third', options: [
                'priority' => 3,
                'visible' => true,
                'export' => [
                    'priority' => 100,
                ],
            ])
            ->addColumn('fourth', options: [
                'priority' => 4,
                'visible' => false,
                'export' => [
                    'visible' => true,
                    'personalizable' => false,
                ],
            ])
            ->addColumn('fifth', options: [
                'priority' => 5,
                'visible' => true,
                'export' => false,
            ])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5, 'visible' => false],
                'second' => ['priority' => 4, 'visible' => true],
                'third' => ['priority' => 3, 'visible' => false],
                'fourth' => ['priority' => 2, 'visible' => false],
                'fifth' => ['priority' => 1, 'visible' => false],
            ],
        ]));

        $columns = array_keys($dataTable->getExportableColumns());

        $this->assertEquals(['second', 'fourth'], $columns);
    }

    public function testGetExportableColumnsIgnoresDisabledPersonalization()
    {
        $dataTable = $this->createDataTableBuilder(['personalization_enabled' => false])
            ->addColumn('first', options: [
                'priority' => 1,
                'visible' => true,
                'export' => true,
            ])
            ->addColumn('second', options: [
                'priority' => 2,
                'visible' => true,
                'export' => [
                    'visible' => false,
                ],
            ])
            ->addColumn('third', options: [
                'priority' => 3,
                'visible' => true,
                'export' => [
                    'priority' => 100,
                ],
            ])
            ->addColumn('fourth', options: [
                'priority' => 4,
                'visible' => false,
                'export' => [
                    'visible' => true,
                ],
            ])
            ->addColumn('fifth', options: [
                'priority' => 5,
                'visible' => true,
                'export' => false,
            ])
            ->getDataTable();

        $dataTable->setPersonalizationData(PersonalizationData::fromArray([
            'columns' => [
                'first' => ['priority' => 5, 'visible' => false],
                'second' => ['priority' => 4, 'visible' => true],
                'third' => ['priority' => 3, 'visible' => false],
                'fourth' => ['priority' => 2, 'visible' => false],
                'fifth' => ['priority' => 1, 'visible' => false],
            ],
        ]));

        $columns = array_keys($dataTable->getExportableColumns());

        $this->assertEquals(['third', 'fourth', 'first'], $columns);
    }

    private function createDataTableBuilder(array $options = []): DataTableBuilderInterface
    {
        return $this->dataTableFactory->createBuilder(DataTableType::class, [], $options);
    }
}
