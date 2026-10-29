<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\KreyuDataTableBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Minimal real kernel that boots FrameworkBundle, TwigBundle and the data table
 * bundle to verify the bundle's DI wiring compiles under the resolved Symfony.
 */
final class DataTableTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new KreyuDataTableBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, levels: 2);
    }

    public function getCacheDir(): string
    {
        return self::tempDir().'/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return self::tempDir().'/log';
    }

    public static function tempDir(): string
    {
        $processId = getmypid();

        if ($processId === false) {
            throw new \RuntimeException('Unable to read the current process ID.');
        }

        return sys_get_temp_dir().'/kreyu_dtb_integration_'.$processId;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'secret' => bin2hex(random_bytes(16)),
            'test' => true,
            'http_method_override' => false,
            'asset_mapper' => ['enabled' => false],
        ]);

        $container->loadFromExtension('twig', [
            'default_path' => $this->getProjectDir().'/templates',
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///:memory:'],
            'orm' => [],
        ]);
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                foreach ([DataTableFactoryInterface::class, 'kreyu_data_table.twig.data_table_extension'] as $id) {
                    $hasAlias = $container->hasAlias($id);

                    if ($hasAlias) {
                        $container->getAlias($id)->setPublic(true);

                        continue;
                    }

                    $hasDefinition = $container->hasDefinition($id);

                    if ($hasDefinition) {
                        $container->getDefinition($id)->setPublic(true);
                    }
                }
            }
        }, PassConfig::TYPE_BEFORE_REMOVING);
    }
}
