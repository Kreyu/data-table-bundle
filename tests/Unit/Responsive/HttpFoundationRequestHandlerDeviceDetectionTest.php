<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Responsive;

use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Request\HttpFoundationRequestHandler;
use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use Kreyu\Bundle\DataTableBundle\Responsive\DeviceDetectorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class HttpFoundationRequestHandlerDeviceDetectionTest extends TestCase
{
    private const DEFAULT_BREAKPOINTS = [
        'sm' => 576,
        'md' => 768,
        'lg' => 992,
        'xl' => 1200,
    ];

    public function testBreakpointParameterOverridesUserAgent(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->never())->method('detect');

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setActiveBreakpoint')
            ->with('sm');

        $handler->handle($dataTable, $this->createRequest(['_breakpoint' => 'sm']));
    }

    public function testFallsBackToUserAgentWhenNoBreakpointParameter(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->once())
            ->method('detect')
            ->willReturn(Device::Tablet);

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setActiveBreakpoint')
            ->with('lg'); // Tablet → median upper breakpoint (lg for 4 breakpoints)

        $handler->handle($dataTable, $this->createRequest());
    }

    public function testFallsBackToUserAgentPhoneGivesSmallestBreakpoint(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->once())
            ->method('detect')
            ->willReturn(Device::Phone);

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setActiveBreakpoint')
            ->with('sm');

        $handler->handle($dataTable, $this->createRequest());
    }

    public function testFallsBackToUserAgentDesktopGivesLargestBreakpoint(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->once())
            ->method('detect')
            ->willReturn(Device::Desktop);

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setActiveBreakpoint')
            ->with('xl'); // Desktop → largest breakpoint

        $handler->handle($dataTable, $this->createRequest());
    }

    public function testInvalidBreakpointParameterFallsBackToUserAgent(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->once())
            ->method('detect')
            ->willReturn(Device::Desktop);

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setActiveBreakpoint')
            ->with('xl'); // Falls back to UA (Desktop → largest)

        $handler->handle($dataTable, $this->createRequest(['_breakpoint' => 'invalid']));
    }

    public function testNoDeviceDetectionWithoutDetector(): void
    {
        $handler = new HttpFoundationRequestHandler();

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->never())->method('setActiveBreakpoint');

        $handler->handle($dataTable, $this->createRequest());
    }

    public function testBreakpointParameterWorksWithoutDetector(): void
    {
        $handler = new HttpFoundationRequestHandler();

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setActiveBreakpoint')
            ->with('md');

        $handler->handle($dataTable, $this->createRequest(['_breakpoint' => 'md']));
    }

    public function testNullRequestDoesNothing(): void
    {
        $handler = new HttpFoundationRequestHandler();

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->never())->method('setActiveBreakpoint');

        $handler->handle($dataTable, null);
    }

    private function createRequest(array $query = []): Request
    {
        $request = Request::create('/?' . http_build_query($query));
        $request->headers->set('Turbo-Frame', 'kreyu_data_table_test');

        return $request;
    }

    private function createDataTableMock(): DataTableInterface&\PHPUnit\Framework\MockObject\MockObject
    {
        $config = $this->createMock(DataTableConfigInterface::class);
        $config->method('isFiltrationEnabled')->willReturn(false);
        $config->method('isSortingEnabled')->willReturn(false);
        $config->method('isPaginationEnabled')->willReturn(false);
        $config->method('isPersonalizationEnabled')->willReturn(false);
        $config->method('isExportingEnabled')->willReturn(false);
        $config->method('getOption')
            ->willReturnCallback(function (string $name) {
                return match ($name) {
                    'responsive_enabled' => true,
                    'responsive_breakpoints' => self::DEFAULT_BREAKPOINTS,
                    default => null,
                };
            });

        $dataTable = $this->createMock(DataTableInterface::class);
        $dataTable->method('getConfig')->willReturn($config);

        return $dataTable;
    }
}
