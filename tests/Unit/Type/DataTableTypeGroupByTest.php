<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Type;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Test\DataTableIntegrationTestCase;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableTypeGroupByTest extends DataTableIntegrationTestCase
{
    public function testGroupByInsertsHeaderRowsAtGroupValueChanges(): void
    {
        $items = $this->createGroupedItems();
        $dataTable = $this->createGroupedDataTable($items, perPage: 25);
        $rows = $this->getGroupedRows($dataTable);
        $exportRowTypes = [];

        foreach ($dataTable->createExportView()->valueRows as $row) {
            $exportRowTypes[] = $row->vars['row_type'];
        }

        $this->assertEquals(
            [
                ['group_header', new \DateTimeImmutable('2024-01-01')],
                ['value', $items[1]],
                ['value', $items[3]],
                ['group_header', new \DateTimeImmutable('2024-01-02')],
                ['value', $items[0]],
                ['value', $items[2]],
            ],
            $rows,
        );
        $this->assertSame(['value', 'value', 'value', 'value'], $exportRowTypes);
    }

    public function testGroupByKeepsPageSize(): void
    {
        $items = $this->createGroupedItems();
        $dataTable = $this->createGroupedDataTable($items, perPage: 2);

        $this->assertEquals(
            [
                ['group_header', new \DateTimeImmutable('2024-01-01')],
                ['value', $items[1]],
                ['value', $items[3]],
            ],
            $this->getGroupedRows($dataTable),
        );
    }

    private function createGroupedItems(): array
    {
        return [
            ['id' => 1, 'date' => new \DateTimeImmutable('2024-01-02')],
            ['id' => 2, 'date' => new \DateTimeImmutable('2024-01-01')],
            ['id' => 3, 'date' => new \DateTimeImmutable('2024-01-02')],
            ['id' => 4, 'date' => new \DateTimeImmutable('2024-01-01')],
        ];
    }

    private function createGroupedDataTable(array $items, int $perPage): DataTableInterface
    {
        $builder = $this->dataTableFactory->createBuilder(DataTableType::class, [], ['group_by' => '[date]']);
        $builder->setQuery(new ArrayProxyQuery($items));
        $builder->setDefaultPaginationData(new PaginationData(perPage: $perPage));
        $builder->addColumn('id', options: ['property_path' => '[id]']);

        return $builder->getDataTable();
    }

    private function getGroupedRows(DataTableInterface $dataTable): array
    {
        $rows = [];

        foreach ($dataTable->createView()->valueRows as $row) {
            $rows[] = [$row->vars['row_type'], $row->data ?? $row->vars['group_value']];
        }

        return $rows;
    }
}
