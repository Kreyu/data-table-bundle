<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Request;

use Kreyu\Bundle\DataTableBundle\DataTable;
use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Request\HttpFoundationRequestHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class HttpFoundationRequestHandlerTest extends TestCase
{
    private HttpFoundationRequestHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new HttpFoundationRequestHandler();
    }

    public function testHandlingFiltrationFormWithFiltrationEnabled()
    {
        $dataTable = $this->createDataTableMock();
        $dataTable->getConfig()->method('isFiltrationEnabled')->willReturn(true);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest')->with();

        $dataTable->method('getFiltrationForm')->willReturn($form);

        $this->handler->handle($dataTable, $this->createMock(Request::class));
    }

    public function testHandlingFiltrationFormWithFiltrationDisabled()
    {
        $dataTable = $this->createMock(DataTable::class);
        $dataTable->method('isFiltrationEnabled')->willReturn(false);
        $dataTable->expects($this->never())->method('getFiltrationForm');
    }

    private function createDataTableMock(): MockObject&DataTableInterface
    {
        $config = $this->createMock(DataTableConfigInterface::class);

        $dataTable = $this->createMock(DataTable::class);
        $dataTable->method('getConfig')->willReturn($config);

        return $dataTable;
    }
}