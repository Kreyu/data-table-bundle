<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;

abstract class DataTableTypeTestCase extends DataTableIntegrationTestCase
{
    public function testPassingNameToView(): void
    {
        $view = $this->createNamedDataTable('foo')->createView();

        $this->assertEquals('foo', $view->vars['name']);
    }

    public function testPassingTitleToView(): void
    {
        $view = $this->createDataTable(['title' => 'foo'])->createView();

        $this->assertEquals('foo', $view->vars['title']);
    }

    public function testPassingThemesAsOption(): void
    {
        $themes = ['foo', 'bar', 'baz'];

        $dataTable = $this->createDataTable(['themes' => $themes]);

        $this->assertEquals($themes, $dataTable->getConfig()->getThemes());
    }

    public function testPassingColumnFactoryAsOption(): void
    {
        $factory = $this->createMock(ColumnFactoryInterface::class);

        $dataTable = $this->createDataTable(['column_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getColumnFactory());
    }

    public function testPassingFilterFactoryAsOption(): void
    {
        $factory = $this->createMock(FilterFactoryInterface::class);

        $dataTable = $this->createDataTable(['filter_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getFilterFactory());
    }

    public function testPassingActionFactoryAsOption(): void
    {
        $factory = $this->createMock(ActionFactoryInterface::class);

        $dataTable = $this->createDataTable(['action_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getActionFactory());
    }

    public function testPassingExporterFactoryAsOption(): void
    {
        $factory = $this->createMock(ExporterFactoryInterface::class);

        $dataTable = $this->createDataTable(['exporter_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getExporterFactory());
    }

    public function testPassingRequestHandlerAsOption(): void
    {
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $dataTable = $this->createDataTable(['request_handler' => $requestHandler]);

        $this->assertSame($requestHandler, $dataTable->getConfig()->getRequestHandler());
    }

    public function testPassingSortingEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['sorting_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isSortingEnabled());

        $dataTable = $this->createDataTable(['sorting_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isSortingEnabled());
    }

    public function testPassingSortingPersistenceEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['sorting_persistence_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isSortingPersistenceEnabled());

        $dataTable = $this->createDataTable(['sorting_persistence_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isSortingPersistenceEnabled());
    }

    public function testPassingSortingPersistenceAdapterAsOption(): void
    {
        $adapter = $this->createMock(PersistenceAdapterInterface::class);

        $dataTable = $this->createDataTable(['sorting_persistence_adapter' => $adapter]);

        $this->assertSame($adapter, $dataTable->getConfig()->getSortingPersistenceAdapter());
    }

    public function testPassingSortingPersistenceSubjectProviderAsOption(): void
    {
        $provider = $this->createMock(PersistenceSubjectProviderInterface::class);

        $dataTable = $this->createDataTable(['sorting_persistence_subject_provider' => $provider]);

        $this->assertSame($provider, $dataTable->getConfig()->getSortingPersistenceSubjectProvider());
    }

    public function testPassingPaginationEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['pagination_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isPaginationEnabled());

        $dataTable = $this->createDataTable(['pagination_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isPaginationEnabled());
    }

    public function testPassingPaginationPersistenceEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['pagination_persistence_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isPaginationPersistenceEnabled());

        $dataTable = $this->createDataTable(['pagination_persistence_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isPaginationPersistenceEnabled());
    }

    public function testPassingPaginationPersistenceAdapterAsOption(): void
    {
        $adapter = $this->createMock(PersistenceAdapterInterface::class);

        $dataTable = $this->createDataTable(['pagination_persistence_adapter' => $adapter]);

        $this->assertSame($adapter, $dataTable->getConfig()->getPaginationPersistenceAdapter());
    }

    public function testPassingPaginationPersistenceSubjectProviderAsOption(): void
    {
        $provider = $this->createMock(PersistenceSubjectProviderInterface::class);

        $dataTable = $this->createDataTable(['pagination_persistence_subject_provider' => $provider]);

        $this->assertSame($provider, $dataTable->getConfig()->getPaginationPersistenceSubjectProvider());
    }

    public function testPassingFiltrationEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['filtration_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isFiltrationEnabled());

        $dataTable = $this->createDataTable(['filtration_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isFiltrationEnabled());
    }

    public function testPassingFiltrationPersistenceEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['filtration_persistence_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isFiltrationPersistenceEnabled());

        $dataTable = $this->createDataTable(['filtration_persistence_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isFiltrationPersistenceEnabled());
    }

    public function testPassingFiltrationPersistenceAdapterAsOption(): void
    {
        $adapter = $this->createMock(PersistenceAdapterInterface::class);

        $dataTable = $this->createDataTable(['filtration_persistence_adapter' => $adapter]);

        $this->assertSame($adapter, $dataTable->getConfig()->getFiltrationPersistenceAdapter());
    }

    public function testPassingFiltrationPersistenceSubjectProviderAsOption(): void
    {
        $provider = $this->createMock(PersistenceSubjectProviderInterface::class);

        $dataTable = $this->createDataTable(['filtration_persistence_subject_provider' => $provider]);

        $this->assertSame($provider, $dataTable->getConfig()->getFiltrationPersistenceSubjectProvider());
    }

    public function testPassingFiltrationFormFactoryAsOption(): void
    {
        $factory = $this->createMock(FormFactoryInterface::class);

        $dataTable = $this->createDataTable(['filtration_form_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getFiltrationFormFactory());
    }

    public function testPassingPersonalizationEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['personalization_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isPersonalizationEnabled());

        $dataTable = $this->createDataTable(['personalization_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isPersonalizationEnabled());
    }

    public function testPassingPersonalizationPersistenceEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['personalization_persistence_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isPersonalizationPersistenceEnabled());

        $dataTable = $this->createDataTable(['personalization_persistence_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isPersonalizationPersistenceEnabled());
    }

    public function testPassingPersonalizationPersistenceAdapterAsOption(): void
    {
        $adapter = $this->createMock(PersistenceAdapterInterface::class);

        $dataTable = $this->createDataTable(['personalization_persistence_adapter' => $adapter]);

        $this->assertSame($adapter, $dataTable->getConfig()->getPersonalizationPersistenceAdapter());
    }

    public function testPassingPersonalizationPersistenceSubjectProviderAsOption(): void
    {
        $provider = $this->createMock(PersistenceSubjectProviderInterface::class);

        $dataTable = $this->createDataTable(['personalization_persistence_subject_provider' => $provider]);

        $this->assertSame($provider, $dataTable->getConfig()->getPersonalizationPersistenceSubjectProvider());
    }

    public function testPassingPersonalizationFormFactoryAsOption(): void
    {
        $factory = $this->createMock(FormFactoryInterface::class);

        $dataTable = $this->createDataTable(['personalization_form_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getPersonalizationFormFactory());
    }

    public function testPassingExportingEnabledAsOption(): void
    {
        $dataTable = $this->createDataTable(['exporting_enabled' => true]);

        $this->assertTrue($dataTable->getConfig()->isExportingEnabled());

        $dataTable = $this->createDataTable(['exporting_enabled' => false]);

        $this->assertFalse($dataTable->getConfig()->isExportingEnabled());
    }

    public function testPassingExportFormFactoryAsOption(): void
    {
        $factory = $this->createMock(FormFactoryInterface::class);

        $dataTable = $this->createDataTable(['exporting_form_factory' => $factory]);

        $this->assertSame($factory, $dataTable->getConfig()->getExportFormFactory());
    }

    private function getQuery(): ProxyQueryInterface
    {
        return $this->createMock(ProxyQueryInterface::class);
    }

    private function createDataTable(array $options = []): DataTableInterface
    {
        return $this->factory->create($this->getTestedType(), $this->getQuery(), $options);
    }

    private function createNamedDataTable(string $name, array $options = []): DataTableInterface
    {
        return $this->factory->createNamed($name, $this->getTestedType(), $this->getQuery(), $options);
    }
}