<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Request;

use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Request\HttpFoundationRequestHandler;
use Kreyu\Bundle\DataTableBundle\Test\DataTableIntegrationTestCase;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\HttpFoundation\Request;

class HttpFoundationRequestHandlerTest extends DataTableIntegrationTestCase
{
    public function testColumnVisibilityGroupUsesRequestedWhenValid()
    {
        $dataTable = $this->createDataTable(['foo', 'bar']);

        (new HttpFoundationRequestHandler())->handle(
            $dataTable,
            new Request([$this->parameterName($dataTable) => 'bar']),
        );

        $this->assertSame('bar', $dataTable->getRequestedColumnVisibilityGroup());
    }

    public function testColumnVisibilityGroupFallsBackToDefaultWhenParamMissing()
    {
        $dataTable = $this->createDataTable(['foo', 'bar'], defaultName: 'bar');

        (new HttpFoundationRequestHandler())->handle($dataTable, new Request());

        $this->assertSame('bar', $dataTable->getRequestedColumnVisibilityGroup());
    }

    public function testColumnVisibilityGroupFallsBackToFirstWhenNoDefault()
    {
        $dataTable = $this->createDataTable(['foo', 'bar']);

        (new HttpFoundationRequestHandler())->handle($dataTable, new Request());

        $this->assertSame('foo', $dataTable->getRequestedColumnVisibilityGroup());
    }

    public function testColumnVisibilityGroupIgnoresUnknownRequestedAndUsesDefault()
    {
        $dataTable = $this->createDataTable(['foo', 'bar'], defaultName: 'bar');

        (new HttpFoundationRequestHandler())->handle(
            $dataTable,
            new Request([$this->parameterName($dataTable) => 'unknown']),
        );

        $this->assertSame('bar', $dataTable->getRequestedColumnVisibilityGroup());
    }

    public function testColumnVisibilityGroupIsNoOpWhenNoGroupsConfigured()
    {
        $dataTable = $this->createDataTable([]);

        (new HttpFoundationRequestHandler())->handle(
            $dataTable,
            new Request([$this->parameterName($dataTable) => 'foo']),
        );

        $this->assertNull($dataTable->getRequestedColumnVisibilityGroup());
    }

    private function parameterName(DataTableInterface $dataTable): string
    {
        return $dataTable->getConfig()->getColumnVisibilityGroupParameterName();
    }

    /**
     * @param list<string> $groupNames
     */
    private function createDataTable(array $groupNames, ?string $defaultName = null): DataTableInterface
    {
        $builder = $this->dataTableFactory->createBuilder(DataTableType::class, []);
        $builder->addColumn('name', TextColumnType::class);

        foreach ($groupNames as $name) {
            $builder->addColumnVisibilityGroup($name, [
                'is_default' => $name === $defaultName,
            ]);
        }

        return $builder->getDataTable();
    }
}
