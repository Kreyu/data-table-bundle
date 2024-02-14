<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableConfigBuilder;
use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\FormFactoryInterface;

class DataTableConfigBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testAddEventListener()
    {
        $listener = fn () => null;

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('addListener')->with('foo', $listener, 100);

        $this->createBuilder(dispatcher: $dispatcher)->addEventListener('foo', $listener, 100);
    }

    public function testAddEventSubscriber()
    {
        $subscriber = $this->createStub(EventSubscriberInterface::class);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('addSubscriber')->with($subscriber);

        $this->createBuilder(dispatcher: $dispatcher)->addEventSubscriber($subscriber);
    }

    public function testGetEventDispatcherReturnsImmutable()
    {
        $dispatcher = $this->createStub(EventDispatcherInterface::class);

        $immutableDispatcher = $this->createBuilder(dispatcher: $dispatcher)->getEventDispatcher();

        $this->assertInstanceOf(ImmutableEventDispatcher::class, $immutableDispatcher);
        $this->assertSame($dispatcher, $this->getPrivatePropertyValue($immutableDispatcher, 'dispatcher'));
    }

    public function testGetName()
    {
        $this->assertSame('foo', $this->createBuilder()->getName());
    }

    public function testGetType()
    {
        $type = $this->createStub(ResolvedDataTableTypeInterface::class);

        $this->assertSame($type, $this->createBuilder(type: $type)->getType());
    }

    public function testGetOptions()
    {
        $this->assertSame(['foo' => 'bar'], $this->createBuilder(options: ['foo' => 'bar'])->getOptions());
    }

    public function testGetOption()
    {
        $this->assertSame('bar', $this->createBuilder(options: ['foo' => 'bar'])->getOption('foo'));
    }

    public function testGetOptionDefault()
    {
        $this->assertSame('bar', $this->createBuilder()->getOption('foo', 'bar'));
    }

    public function testHasOption()
    {
        $builder = $this->createBuilder(options: ['foo' => 'bar']);

        $this->assertTrue($builder->hasOption('foo'));
        $this->assertFalse($builder->hasOption('bar'));
    }

    public function testIsPaginationEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setPaginationEnabled(true);

        $this->assertTrue($builder->isPaginationEnabled());
    }

    public function testIsSortingEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setSortingEnabled(true);

        $this->assertTrue($builder->isSortingEnabled());
    }

    public function testIsFiltrationEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setFiltrationEnabled(true);

        $this->assertTrue($builder->isFiltrationEnabled());
    }

    public function testIsPersonalizationEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setPersonalizationEnabled(true);

        $this->assertTrue($builder->isPersonalizationEnabled());
    }

    public function testIsExportingEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setExportingEnabled(true);

        $this->assertTrue($builder->isExportingEnabled());
    }

    public function testIsPaginationPersistenceEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setPaginationPersistenceEnabled(true);

        $this->assertTrue($builder->isPaginationPersistenceEnabled());
    }

    public function testIsSortingPersistenceEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setSortingPersistenceEnabled(true);

        $this->assertTrue($builder->isSortingPersistenceEnabled());
    }

    public function testIsFiltrationPersistenceEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setFiltrationPersistenceEnabled(true);

        $this->assertTrue($builder->isFiltrationPersistenceEnabled());
    }

    public function testIsPersonalizationPersistenceEnabled()
    {
        $builder = $this->createBuilder();
        $builder->setPersonalizationPersistenceEnabled(true);

        $this->assertTrue($builder->isPersonalizationPersistenceEnabled());
    }

    public function testGetPaginationPersistenceAdapter()
    {
        $adapter = $this->createStub(PersistenceAdapterInterface::class);

        $builder = $this->createBuilder();
        $builder->setPaginationPersistenceAdapter($adapter);

        $this->assertSame($adapter, $builder->getPaginationPersistenceAdapter());
    }

    public function testGetSortingPersistenceAdapter()
    {
        $adapter = $this->createStub(PersistenceAdapterInterface::class);

        $builder = $this->createBuilder();
        $builder->setSortingPersistenceAdapter($adapter);

        $this->assertSame($adapter, $builder->getSortingPersistenceAdapter());
    }

    public function testGetFiltrationPersistenceAdapter()
    {
        $adapter = $this->createStub(PersistenceAdapterInterface::class);

        $builder = $this->createBuilder();
        $builder->setFiltrationPersistenceAdapter($adapter);

        $this->assertSame($adapter, $builder->getFiltrationPersistenceAdapter());
    }

    public function testGetPersonalizationPersistenceAdapter()
    {
        $adapter = $this->createStub(PersistenceAdapterInterface::class);

        $builder = $this->createBuilder();
        $builder->setPersonalizationPersistenceAdapter($adapter);

        $this->assertSame($adapter, $builder->getPersonalizationPersistenceAdapter());
    }

    public function testGetPaginationPersistenceSubjectProvider()
    {
        $provider = $this->createStub(PersistenceSubjectProviderInterface::class);

        $builder = $this->createBuilder();
        $builder->setPaginationPersistenceSubjectProvider($provider);

        $this->assertSame($provider, $builder->getPaginationPersistenceSubjectProvider());
    }

    public function testGetSortingPersistenceSubjectProvider()
    {
        $provider = $this->createStub(PersistenceSubjectProviderInterface::class);

        $builder = $this->createBuilder();
        $builder->setSortingPersistenceSubjectProvider($provider);

        $this->assertSame($provider, $builder->getSortingPersistenceSubjectProvider());
    }

    public function testGetFiltrationPersistenceSubjectProvider()
    {
        $provider = $this->createStub(PersistenceSubjectProviderInterface::class);

        $builder = $this->createBuilder();
        $builder->setFiltrationPersistenceSubjectProvider($provider);

        $this->assertSame($provider, $builder->getFiltrationPersistenceSubjectProvider());
    }

    public function testGetPersonalizationPersistenceSubjectProvider()
    {
        $provider = $this->createStub(PersistenceSubjectProviderInterface::class);

        $builder = $this->createBuilder();
        $builder->setPersonalizationPersistenceSubjectProvider($provider);

        $this->assertSame($provider, $builder->getPersonalizationPersistenceSubjectProvider());
    }

    public function testGetFiltrationFormFactory()
    {
        $factory = $this->createStub(FormFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setFiltrationFormFactory($factory);

        $this->assertSame($factory, $builder->getFiltrationFormFactory());
    }

    public function testGetPersonalizationFormFactory()
    {
        $factory = $this->createStub(FormFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setPersonalizationFormFactory($factory);

        $this->assertSame($factory, $builder->getPersonalizationFormFactory());
    }

    public function testGetExportFormFactory()
    {
        $factory = $this->createStub(FormFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setExportFormFactory($factory);

        $this->assertSame($factory, $builder->getExportFormFactory());
    }

    public function testGetDefaultPaginationData()
    {
        $builder = $this->createBuilder()->setDefaultPaginationData($data = new PaginationData());

        $this->assertSame($data, $builder->getDefaultPaginationData());
    }

    public function testGetDefaultSortingData()
    {
        $builder = $this->createBuilder()->setDefaultSortingData($data = new SortingData());

        $this->assertSame($data, $builder->getDefaultSortingData());
    }

    public function testGetDefaultFiltrationData()
    {
        $builder = $this->createBuilder()->setDefaultFiltrationData($data = new FiltrationData());

        $this->assertSame($data, $builder->getDefaultFiltrationData());
    }

    public function testGetDefaultPersonalizationData()
    {
        $builder = $this->createBuilder()->setDefaultPersonalizationData($data = new PersonalizationData());

        $this->assertSame($data, $builder->getDefaultPersonalizationData());
    }

    public function testGetDefaultExportData()
    {
        $builder = $this->createBuilder()->setDefaultExportData($data = new ExportData());

        $this->assertSame($data, $builder->getDefaultExportData());
    }

    public function testGetColumnFactory()
    {
        $factory = $this->createStub(ColumnFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setColumnFactory($factory);

        $this->assertSame($factory, $builder->getColumnFactory());
    }

    public function testGetColumnFactoryWithoutFactorySet()
    {
        $this->expectException(BadMethodCallException::class);
        $this->createBuilder()->getColumnFactory();
    }

    public function testGetFilterFactory()
    {
        $factory = $this->createStub(FilterFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setFilterFactory($factory);

        $this->assertSame($factory, $builder->getFilterFactory());
    }

    public function testGetFilterFactoryWithoutFactorySet()
    {
        $this->expectException(BadMethodCallException::class);
        $this->createBuilder()->getFilterFactory();
    }

    public function testGetActionFactory()
    {
        $factory = $this->createStub(ActionFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setActionFactory($factory);

        $this->assertSame($factory, $builder->getActionFactory());
    }

    public function testGetActionFactoryWithoutFactorySet()
    {
        $this->expectException(BadMethodCallException::class);
        $this->createBuilder()->getActionFactory();
    }

    public function testGetExporterFactory()
    {
        $factory = $this->createStub(ExporterFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setExporterFactory($factory);

        $this->assertSame($factory, $builder->getExporterFactory());
    }

    public function testGetExporterFactoryWithoutFactorySet()
    {
        $this->expectException(BadMethodCallException::class);
        $this->createBuilder()->getExporterFactory();
    }

    public function testGetThemes()
    {
        $builder = $this->createBuilder();
        $builder->setThemes(['foo', 'bar']);
        $builder->addTheme('baz');

        $this->assertSame(['foo', 'bar', 'baz'], $builder->getThemes());
    }

    public function testGetHeaderRowAttributes()
    {
        $builder = $this->createBuilder();
        $builder->setHeaderRowAttributes(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $builder->getHeaderRowAttributes());
    }

    public function testGetHeaderRowAttribute()
    {
        $builder = $this->createBuilder();
        $builder->setHeaderRowAttribute('foo', 'bar');

        $this->assertSame('bar', $builder->getHeaderRowAttribute('foo'));
    }

    public function testGetHeaderRowAttributeDefault()
    {
        $this->assertSame('bar', $this->createBuilder()->getHeaderRowAttribute('foo', 'bar'));
    }

    public function testHasHeaderRowAttribute()
    {
        $builder = $this->createBuilder();
        $builder->setHeaderRowAttribute('foo', 'bar');

        $this->assertTrue($builder->hasHeaderRowAttribute('foo'));
        $this->assertFalse($builder->hasHeaderRowAttribute('bar'));
    }

    public function testGetValueRowAttributes()
    {
        $builder = $this->createBuilder();
        $builder->setValueRowAttributes(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $builder->getValueRowAttributes());
    }

    public function testGetValueRowAttribute()
    {
        $builder = $this->createBuilder();
        $builder->setValueRowAttribute('foo', 'bar');

        $this->assertSame('bar', $builder->getValueRowAttribute('foo'));
    }

    public function testGetValueRowAttributeDefault()
    {
        $this->assertSame('bar', $this->createBuilder()->getValueRowAttribute('foo', 'bar'));
    }

    public function testHasValueRowAttribute()
    {
        $builder = $this->createBuilder();
        $builder->setValueRowAttribute('foo', 'bar');

        $this->assertTrue($builder->hasValueRowAttribute('foo'));
        $this->assertFalse($builder->hasValueRowAttribute('bar'));
    }

    public function testGetPageParameterName()
    {
        $this->assertSame('page_foo', $this->createBuilder()->getPageParameterName());
    }

    public function testGetPerPageParameterName()
    {
        $this->assertSame('limit_foo', $this->createBuilder()->getPerPageParameterName());
    }

    public function testGetSortParameterName()
    {
        $this->assertSame('sort_foo', $this->createBuilder()->getSortParameterName());
    }

    public function testGetFiltrationParameterName()
    {
        $this->assertSame('filter_foo', $this->createBuilder()->getFiltrationParameterName());
    }

    public function testGetPersonalizationParameterName()
    {
        $this->assertSame('personalization_foo', $this->createBuilder()->getPersonalizationParameterName());
    }

    public function testGetExportParameterName()
    {
        $this->assertSame('export_foo', $this->createBuilder()->getExportParameterName());
    }

    public function testGetDataTableConfig()
    {
        $config = $this->createBuilder()->getDataTableConfig();

        $this->assertInstanceOf(DataTableConfigInterface::class, $config);
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(?ResolvedDataTableTypeInterface $type = null, ?EventDispatcherInterface $dispatcher = null, array $options = []): DataTableConfigBuilder
    {
        return new DataTableConfigBuilder(
            name: 'foo',
            type: $type ?? $this->createStub(ResolvedDataTableTypeInterface::class),
            dispatcher: $dispatcher ?? $this->createStub(EventDispatcherInterface::class),
            options: $options,
        );
    }
}
